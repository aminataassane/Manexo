<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(__('platform_login.page_title')); ?> — <?php echo e(config('app.name', 'Manexo')); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/Logo(1).png')); ?>">

    <script defer src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet"></noscript>

    <style>
        body, .font-serif {
            font-family: "Mona Sans", sans-serif;
            font-optical-sizing: auto;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }

        .fade-in { animation: fadeIn 0.6s ease-out forwards; opacity: 0; transform: translateY(10px); }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }

        .grid-pattern {
            background-image:
                linear-gradient(rgba(99, 102, 241, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99, 102, 241, 0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .animate-pulse-slow { animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="overflow-x-hidden flex items-center justify-center w-full min-h-screen min-h-[100dvh] relative bg-slate-950" style="padding-left: max(1rem, env(safe-area-inset-left)); padding-right: max(1rem, env(safe-area-inset-right)); padding-top: max(1.5rem, env(safe-area-inset-top)); padding-bottom: max(1.5rem, env(safe-area-inset-bottom));">

    
    <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-indigo-950/50 to-slate-950"></div>
    <div class="absolute inset-0 grid-pattern"></div>

    
    <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-0 right-1/4 w-[400px] h-[400px] bg-violet-600/10 rounded-full blur-[100px]"></div>

    
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none hidden md:block">
        
        <div class="absolute top-[18%] left-[12%] opacity-10">
            <iconify-icon icon="solar:shield-star-bold-duotone" width="80" class="text-indigo-400"></iconify-icon>
        </div>

        
        <div class="absolute bottom-[20%] right-[10%] opacity-10">
            <iconify-icon icon="solar:lock-password-bold-duotone" width="60" class="text-violet-400"></iconify-icon>
        </div>

        
        <div class="absolute top-[15%] right-[14%] opacity-60">
            <div class="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm px-4 py-3 shadow-lg">
                <div class="flex items-center gap-2.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse-slow"></span>
                    <span class="text-[11px] font-medium text-slate-400"><?php echo e(__('platform_login.system_ok')); ?></span>
                </div>
            </div>
        </div>

        
        <div class="absolute bottom-[18%] left-[14%] opacity-60">
            <div class="rounded-lg border border-white/10 bg-white/5 backdrop-blur-sm px-3 py-2 shadow-lg">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="solar:verified-check-bold" width="14" class="text-indigo-400"></iconify-icon>
                    <span class="text-[10px] font-semibold text-indigo-300 uppercase tracking-wider"><?php echo e(__('platform_login.admin_access')); ?></span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="relative z-10 w-full min-w-0 max-w-[420px] sm:max-w-[440px] min-[1920px]:max-w-[480px] px-0 sm:px-4">
        <?php echo e($slot); ?>


        <p class="mt-8 text-center text-[10px] text-slate-600 tracking-wide font-medium">© <?php echo e(date('Y')); ?> <?php echo e(config('app.name', 'Manexo')); ?> — <?php echo e(__('platform_login.footer')); ?></p>
    </div>

    <script>if(!window.Echo){var _c={listen:function(){return _c},stopListening:function(){return _c},notification:function(){return _c},listenForWhisper:function(){return _c},subscribed:function(){return _c},error:function(){return _c}};window.Echo={private:function(){return _c},channel:function(){return _c},encryptedPrivate:function(){return _c},join:function(){return _c},leave:function(){},leaveChannel:function(){},leaveAllChannels:function(){},socketId:function(){return null},connector:{pusher:{connection:{state:"stub"}}}}}</script>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Aminata_an\OneDrive\Bureau\QUALITY_CENTER\Manexo\manexo\resources\views\layouts\platform-guest.blade.php ENDPATH**/ ?>