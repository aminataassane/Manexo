<?php

namespace App\Livewire\KnowledgeBase;

use App\Helpers\CacheHelper;
use App\Models\KbArticle;
use App\Models\KbCategory;
use Illuminate\Support\Facades\Cache;
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
        $this->categoryId = $id;
        $this->viewingArticleId = null;
    }

    public function render()
    {
        $orgId = (int) session('current_organization_id');

        $categories = collect();
        $articles = collect();
        $viewingArticle = null;

        if ($orgId) {
            $categories = Cache::remember(CacheHelper::kbCategoriesKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                return KbCategory::withoutOrganizationScope()
                    ->where('organization_id', $orgId)
                    ->where('is_active', true)
                    ->withCount(['articles' => function ($q) {
                        $q->published();
                    }])
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
            });

            $searchTerm = trim($this->search);
            $hasSearch = $searchTerm !== '' && mb_strlen($searchTerm) >= 2;
            $hasCategory = (bool) $this->categoryId;

            // Fast path: opening the KB page (no search/filter) serves from cache.
            if (! $hasSearch && ! $hasCategory) {
                $articles = Cache::remember(CacheHelper::kbArticlesKey($orgId), CacheHelper::TTL, function () use ($orgId) {
                    return KbArticle::withoutOrganizationScope()
                        ->where('organization_id', $orgId)
                        ->published()
                        ->with(['category:id,name,icon'])
                        ->orderByDesc('view_count')
                        ->orderByDesc('published_at')
                        ->limit(50)
                        ->get(['id', 'kb_category_id', 'title', 'content', 'keywords', 'view_count', 'published_at']);
                });
            } else {
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
                            ->orWhereRaw('LOWER(content) LIKE LOWER(?)', [$term])
                            ->orWhereRaw("EXISTS (SELECT 1 FROM jsonb_array_elements_text(COALESCE(keywords, '[]'::jsonb)) kw WHERE LOWER(kw) LIKE LOWER(?))", [$term]);
                    });
                }

                $articles = $query
                    ->orderByDesc('view_count')
                    ->orderByDesc('published_at')
                    ->limit(50)
                    ->get(['id', 'kb_category_id', 'title', 'content', 'keywords', 'view_count', 'published_at']);
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
}
