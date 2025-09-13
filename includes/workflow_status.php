<?php
// Workflow Status Component
function renderWorkflowStatus($workflow) {
    $steps = [
        'consultation_registration' => ['label' => 'Registration & Consultation', 'icon' => 'fas fa-user-md'],
        'lab_tests' => ['label' => 'Lab Tests', 'icon' => 'fas fa-flask'],
        'results_review' => ['label' => 'Results Review', 'icon' => 'fas fa-clipboard-check']
    ];

    echo '<div class="bg-white rounded-lg shadow p-6">';
    echo '<h3 class="text-lg font-medium text-gray-900 mb-4">Workflow Progress</h3>';
    echo '<div class="space-y-3">';

    foreach ($steps as $step => $config) {
        $isCompleted = $workflow[$step . '_paid'];
        $isCurrent = $workflow['current_step'] === $step;

        $bgColor = $isCompleted ? 'bg-green-100' : ($isCurrent ? 'bg-blue-100' : 'bg-gray-100');
        $iconColor = $isCompleted ? 'text-green-600' : ($isCurrent ? 'text-blue-600' : 'text-gray-400');
        $textColor = $isCompleted ? 'text-gray-600' : ($isCurrent ? 'text-gray-900 font-medium' : 'text-gray-400');

        echo '<div class="flex items-center">';
        echo '<div class="w-8 h-8 ' . $bgColor . ' rounded-full flex items-center justify-center mr-3">';
        echo '<i class="' . $config['icon'] . ' ' . $iconColor . '"></i>';
        echo '</div>';
        echo '<span class="text-sm ' . $textColor . '">' . $config['label'] . ' - ' . ($isCompleted ? 'Completed' : 'Pending') . '</span>';
        echo '</div>';
    }

    echo '</div>';
    echo '</div>';
}
?>
