<?php

namespace App\Livewire\KnowledgeBase;

use App\Helpers\CacheHelper;
use App\Models\KbArticle;
use App\Models\KbCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.manexo-app')]
#[Title('Base de connaissances')]
class Index extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public ?int $categoryId = null;

    public ?int $viewingArticleId = null;

    public function updatedSearch(string $value): void
    {
        $next = trim($value);
        if ($next !== $this->search) {
            $this->search = $next;
        }
    }

    public function viewArticle(int $id): void
    {
        $orgId = (int) session('current_organization_id');
        if (! $orgId) {
            return;
        }

        $article = KbArticle::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->published()
            ->where('id', $id)
            ->first();

        if ($article) {
            $article->increment('view_count');
            $this->viewingArticleId = $article->id;
        }
    }

    public function closeArticle(): void
    {
        $this->viewingArticleId = null;
    }

    public function filterByCategory(?int $id): void
    {
        if ($this->categoryId === $id) {
            return;
        }

        $this->categoryId = $id;
        $this->viewingArticleId = null;
    }

    private function articlesCacheKey(int $orgId): string
    {
        $searchTerm = trim($this->search);
        $normalizedSearch = mb_strtolower($searchTerm);
        $category = (int) ($this->categoryId ?? 0);
        $scopeHash = md5($normalizedSearch.'|'.$category);

        return "kb_articles_filtered:{$orgId}:{$scopeHash}";
    }

    public function render()
    {
        $orgId = (int) session('current_organization_id');

        $categories = collect();
        $articles = collect();
        $viewingArticle = null;

        if ($orgId) {
            $searchTerm = trim($this->search);
            $hasSearch = $searchTerm !== '' && mb_strlen($searchTerm) >= 2;
            $hasCategory = (bool) $this->categoryId;

            if (! $hasSearch && ! $hasCategory) {
                $data = Cache::remember(CacheHelper::kbIndexDefaultKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                    return [
                        'categories' => $this->fetchCategoriesForOrg($orgId),
                        'articles' => $this->fetchArticlesListForOrg($orgId),
                    ];
                });
                $categories = $data['categories'];
                $articles = $data['articles'];
            } else {
                $categories = Cache::remember(CacheHelper::kbCategoriesKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                    return $this->fetchCategoriesForOrg($orgId);
                });

                $articles = Cache::remember($this->articlesCacheKey($orgId), CacheHelper::TTL_SHORT, function () use ($orgId, $hasCategory, $hasSearch, $searchTerm) {
                    $query = KbArticle::withoutOrganizationScope()
                        ->where('organization_id', $orgId)
                        ->published()
                        ->with(['category:id,name,icon']);

                    if ($hasCategory) {
                        $query->where('kb_category_id', $this->categoryId);
                    }

                    if ($hasSearch) {
                        $term = '%'.$searchTerm.'%';
                        $query->where(function ($q) use ($term) {
                            $q->whereRaw('LOWER(title) LIKE LOWER(?)', [$term])
                                ->orWhereRaw('LOWER(content) LIKE LOWER(?)', [$term]);
                            if (DB::connection()->getDriverName() === 'pgsql') {
                                $q->orWhereRaw("EXISTS (SELECT 1 FROM jsonb_array_elements_text(COALESCE(keywords, '[]'::jsonb)) kw WHERE LOWER(kw) LIKE LOWER(?))", [$term]);
                            } else {
                                $q->orWhereRaw('LOWER(CAST(keywords AS TEXT)) LIKE LOWER(?)', [$term]);
                            }
                        });
                    }

                    return $this->applyKbListSelect($query)
                        ->orderByDesc('view_count')
                        ->orderByDesc('published_at')
                        ->limit(50)
                        ->get();
                });
            }

            if ($this->viewingArticleId) {
                $viewingArticle = KbArticle::withoutOrganizationScope()
                    ->where('organization_id', $orgId)
                    ->published()
                    ->with('category', 'creator')
                    ->find($this->viewingArticleId);
            }
        }

        return view('livewire.knowledge-base.index', [
            'categories' => $categories,
            'articles' => $articles,
            'viewingArticle' => $viewingArticle,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, KbCategory>
     */
    private function fetchCategoriesForOrg(int $orgId): Collection
    {
        return KbCategory::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->select(['id', 'organization_id', 'name', 'icon', 'sort_order'])
            ->withCount(['articles' => function ($q) {
                $q->published();
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, KbArticle>
     */
    private function fetchArticlesListForOrg(int $orgId): Collection
    {
        $query = KbArticle::withoutOrganizationScope()
            ->where('organization_id', $orgId)
            ->published()
            ->with(['category:id,name,icon']);

        return $this->applyKbListSelect($query)
            ->orderByDesc('view_count')
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();
    }

    /**
     * List cards only need a short excerpt; avoid loading full HTML blobs from DB.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<KbArticle>  $query
     * @return \Illuminate\Database\Eloquent\Builder<KbArticle>
     */
    private function applyKbListSelect($query)
    {
        $table = (new KbArticle)->getTable();

        return $query
            ->select([
                "{$table}.id",
                "{$table}.kb_category_id",
                "{$table}.title",
                "{$table}.keywords",
                "{$table}.view_count",
                "{$table}.published_at",
            ])
            ->addSelect($this->contentSnippetSelect($table));
    }

    private function contentSnippetSelect(string $table): \Illuminate\Database\Query\Expression
    {
        return match (DB::connection()->getDriverName()) {
            'pgsql' => DB::raw("substring({$table}.content::text from 1 for 4000) as content"),
            'sqlite' => DB::raw("SUBSTR({$table}.content, 1, 4000) as content"),
            default => DB::raw("SUBSTRING({$table}.content, 1, 4000) as content"),
        };
    }
}
