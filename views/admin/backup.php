<?php
// Admin backup page
// $backups (array), $csrf_token provided
$BASE_PATH = '/KJ';
?>
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-semibold mb-4">Database Backups</h1>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="p-3 bg-green-50 border border-green-200 rounded mb-4 text-green-800"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="p-3 bg-red-50 border border-red-200 rounded mb-4 text-red-800"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?php echo $BASE_PATH; ?>/admin/backup_database" class="mb-6">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Create Backup Now</button>
        </form>

        <h2 class="text-xl font-medium mb-2">Available Backups</h2>
        <?php if (empty($backups)): ?>
            <p class="text-sm text-gray-600">No backups found.</p>
        <?php else: ?>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Filename</th>
                        <th class="py-2">Size</th>
                        <th class="py-2">Modified</th>
                        <th class="py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $b): ?>
                        <tr class="border-b">
                            <td class="py-2"><?php echo htmlspecialchars($b['name']); ?></td>
                            <td class="py-2"><?php echo round($b['size'] / 1024, 1); ?> KB</td>
                            <td class="py-2"><?php echo date('Y-m-d H:i:s', $b['mtime']); ?></td>
                            <td class="py-2">
                                <a class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700" href="<?php echo $BASE_PATH; ?>/storage/backups/<?php echo rawurlencode($b['name']); ?>" target="_blank">Download</a>
                                <a class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700" href="<?php echo $BASE_PATH; ?>/admin/delete_backup?file=<?php echo rawurlencode($b['name']); ?>" onclick="return confirm('Delete this backup?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
