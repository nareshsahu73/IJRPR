<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Paper Details</h1>
        <div class="space-x-2">
            <a href="<?php echo e(route('papers.index')); ?>" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                Back to List
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->id() === $paper->user_id || auth()->user()->is_admin): ?>
                <form method="POST" action="<?php echo e(route('papers.destroy', $paper)); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                            onclick="return confirm('Are you sure you want to delete this paper?')">
                        Delete Paper
                    </button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Paper Information -->
        <div class="border-b pb-4">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Paper Information</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Title</label>
                    <p class="text-gray-900"><?php echo e($paper->Title ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>

        <!-- Author Details -->
        <div class="border-b pb-4">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Author Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Corresponding Author</label>
                    <p class="text-gray-900"><?php echo e($paper->author_name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Email</label>
                    <p class="text-gray-900"><?php echo e($paper->cer_author_name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Contact Number</label>
                    <p class="text-gray-900"><?php echo e($paper->contact_no ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Position</label>
                    <p class="text-gray-900">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            <?php echo e($paper->position ?? 'N/A'); ?>

                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Affiliation -->
        <div class="border-b pb-4">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Affiliation</h2>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Organization</label>
                    <p class="text-gray-900"><?php echo e($paper->affiliation ?? 'N/A'); ?></p>
                </div>
            </div>
        </div>

        <!-- Description -->
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paper->Abstract): ?>
        <div class="border-b pb-4">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Abstract/Comment</h2>
            <p class="text-gray-900 whitespace-pre-wrap"><?php echo e($paper->Abstract); ?></p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <!-- File -->
        <div class="border-b pb-4">
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Uploaded File</h2>
            <div class="flex items-center space-x-4">
                <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <div>
                    <p class="text-gray-900 font-medium"><?php echo e(basename($paper->file_name ?? 'N/A')); ?></p>
                    <a href="<?php echo e(route('papers.download', $paper)); ?>" 
                       class="inline-block mt-2 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Download File
                    </a>
                </div>
            </div>
        </div>

        <!-- Submission Info -->
        <div>
            <h2 class="text-lg font-semibold mb-3 text-gray-700">Submission Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Submitted By</label>
                    <p class="text-gray-900"><?php echo e($paper->user->name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Submitted On</label>
                    <p class="text-gray-900"><?php echo e($paper->created_at); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/papers/show.blade.php ENDPATH**/ ?>