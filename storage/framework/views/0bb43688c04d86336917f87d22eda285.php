<div class="mb-8">
    
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 dark:from-indigo-900/50 dark:to-indigo-900/20 px-8 py-12 shadow-xl">
        
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-500/5 rounded-full -mr-48 -mt-48 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-400/10 dark:bg-indigo-400/5 rounded-full -ml-40 -mb-40 blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-4 mb-6">
                <div class="p-3 bg-white/20 dark:bg-white/10 rounded-xl backdrop-blur-sm">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-magnifying-glass'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-white']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                </div>
                <div>
                    <h1 class="text-4xl font-bold text-white">Selamat Datang, <?php echo e(auth()->user()->name); ?></h1>
                    <p class="text-indigo-100 mt-1">Team Leader Investigasi | Dashboard Investigasi Barang</p>
                </div>
            </div>
            <p class="text-indigo-50 text-base leading-relaxed max-w-2xl">
                Berikut adalah ringkasan lengkap proses investigasi barang sitaan. Monitor kemajuan investigasi dan pastikan semua prosedur investigasi berjalan dengan baik.
            </p>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        
        
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-100 dark:bg-indigo-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Menunggu Investigasi</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                <?php echo e(\App\Models\ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                                        $query->where('status', 'pending_verification');
                                    })->count()); ?>

                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-indigo-100 to-indigo-50 dark:from-indigo-900/40 dark:to-indigo-900/20 rounded-xl">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-magnifying-glass'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-indigo-600 dark:text-indigo-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang menunggu untuk diinvestigasi lebih lanjut</p>
                </div>
            </div>
        </div>

        
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-violet-600 to-violet-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <div class="absolute top-0 right-0 w-24 h-24 bg-violet-100 dark:bg-violet-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Sedang Diinvestigasi</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                <?php echo e(\App\Models\ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                                        $query->whereIn('status', ['IN_STORAGE', 'PENDING_SHIPMENT_CONFIRMATION']);
                                    })->count()); ?>

                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-violet-100 to-violet-50 dark:from-violet-900/40 dark:to-violet-900/20 rounded-xl">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-document-magnifying-glass'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-violet-600 dark:text-violet-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang sedang dalam proses investigasi mendalam</p>
                </div>
            </div>
        </div>

        
        <div class="relative group">
            <div class="absolute inset-0 bg-gradient-to-br from-cyan-600 to-cyan-700 rounded-2xl blur-lg opacity-25 group-hover:opacity-40 transition-all duration-300"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                
                <div class="absolute top-0 right-0 w-24 h-24 bg-cyan-100 dark:bg-cyan-900/20 rounded-full -mr-12 -mt-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Investigasi Selesai</p>
                            <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">
                                <?php echo e(\App\Models\ConfiscatedItem::whereHas('latestStatusLog', function ($query) {
                                        $query->whereIn('status', ['PICKED_UP', 'SHIPPED', 'DISPOSED', 'HANDED_TO_POLICE']);
                                    })->count()); ?>

                            </p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-cyan-100 to-cyan-50 dark:from-cyan-900/40 dark:to-cyan-900/20 rounded-xl">
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-check-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-cyan-600 dark:text-cyan-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Barang sudah diselesaikan investigasinya</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\ElroiJohanes\Documents\singgah\singgah-project\resources\views/filament/pages/partials/team-leader-investigasi-dashboard-header.blade.php ENDPATH**/ ?>