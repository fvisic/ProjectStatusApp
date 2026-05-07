<?php

return [
    // Navigation & general
    'title' => 'Projects',
    'title_list' => 'Projects - List',
    'title_kanban' => 'Projects - Kanban',
    'title_timeline' => 'Projects - Timeline',
    'new_project' => '+ New Project',
    'edit_project' => 'Edit Project',
    'view_project' => 'View Project',
    'read_only' => 'Read-only',
    'read_only_notice' => 'You are viewing this project in read-only mode. You can only edit projects you created.',
    'view' => 'View',
    'stale_tooltip' => 'No status update in 14+ days',
    'legend_overdue' => 'Go-live date has passed',
    'legend_soon' => 'Go-live within 14 days',
    'legend_stale' => 'No update in 14+ days',
    'back_to_list' => 'Back to list',
    'back' => 'Back',
    'save' => 'Save Project',
    'saving' => 'Saving...',
    'cancel' => 'Cancel',
    'delete' => 'Delete',
    'edit' => 'Edit',
    'history' => 'History',
    'pdf' => 'PDF',
    'actions' => 'Actions',
    'no_projects' => 'No projects',
    'no_projects_hint' => 'Click "New Project" to create your first project.',
    'saved_successfully' => 'Project saved successfully.',
    'saved_no_changes' => 'No changes detected - version not bumped.',
    'snapshot_changes' => 'Updated',
    'confirm_delete' => 'Are you sure you want to delete this project?',

    // Header
    'status_report' => 'Project Status Report',
    'report_subtitle' => 'Filled by TL - updated weekly or on demand',

    // Search & filters
    'search_placeholder' => 'Search projects...',
    'all_statuses' => 'All statuses',
    'all_types' => 'All types',

    // Meta fields
    'project_name' => 'Project',
    'project_name_placeholder' => 'Project name',
    'client' => 'Client',
    'client_placeholder' => 'Client name',
    'team_lead' => 'Project Manager',
    'team_lead_placeholder' => 'Project manager name',
    'report_date' => 'Report Date',
    'project_start' => 'Project Start',
    'planned_go_live' => 'Planned Go-Live',
    'current_phase' => 'Current Phase',
    'overall_health' => 'Overall Health',
    'go_live' => 'Go-Live',

    // Project types
    'type' => 'Type',
    'type_placeholder' => '— select type —',
    'type_new' => 'New Implementation',
    'type_migration' => 'Migration',
    'type_cr' => 'CR / Modification',

    // Health statuses
    'health_on_track' => 'On Track',
    'health_at_risk' => 'At Risk',
    'health_off_track' => 'Off Track',

    // Phases section
    'phases_title' => 'Project Phases & Status',
    'phase' => 'Phase',
    'key_activities' => 'Key Activities',
    'client_confirmation' => 'Client?',
    'status' => 'Status',
    'date' => 'Date',
    'completion_date' => 'Completion Date / est.',
    'notes' => 'Notes',

    // Phase statuses
    'status_pending' => 'Pending',
    'status_in_progress' => 'In Progress',
    'status_done' => 'Done',
    'status_blocked' => 'Blocked',

    // Phase names
    'phase_instalacija' => 'Installation & Analysis',
    'phase_funkcionalna' => 'Functional Specification',
    'phase_implementacija' => 'Implementation & Testing',
    'phase_integracije' => 'Integrations',
    'phase_uat' => 'UAT & Training',
    'phase_golive' => 'Go-Live',
    'phase_hypercare' => 'Hypercare',

    // Estimation section
    'estimation_title' => 'Profitability & Estimation',
    'estimated_hours' => 'Estimated Hours',
    'spent_hours' => 'Spent Hours',
    'remaining_hours' => 'Remaining (est.)',
    'enter_hours' => 'Enter hours to display',
    'estimation_comment' => 'Estimation Comment',
    'estimation_comment_placeholder' => 'Where did we miss? What caused the deviation?',
    'delta_ok' => 'Within estimate (forecast: :forecasth, :pct% below)',
    'delta_warn' => 'Slight overrun: +:deltah (+:pct%) above estimate',
    'delta_over' => 'Overrun: +:deltah (+:pct%) - escalation needed',

    // Next steps
    'next_steps_title' => 'Next Steps',
    'step_placeholder' => 'Step :number...',
    'add_step' => '+ Add Step',

    // Risks
    'risks_title' => 'Risks & Mitigation',
    'risk_description' => 'Risk Description',
    'risk_level' => 'Level',
    'risk_mitigation' => 'Mitigation',
    'risk_description_placeholder' => 'E.g. client delays sign-off',
    'risk_mitigation_placeholder' => 'E.g. escalate to steering committee',
    'add_risk' => '+ Add Risk',
    'add_phase' => '+ Add Phase',
    'remove_phase' => 'Remove phase',
    'drag_to_reorder' => 'Drag to reorder',
    'phase_name_placeholder' => 'Phase name',
    'phase_activities_placeholder' => 'Key activities',
    'phase_client_placeholder' => 'Yes / No / -',
    'level_low' => 'Low',
    'level_medium' => 'Medium',
    'level_high' => 'High',

    // Product notification
    'notification_title' => 'Product Notification',
    'notification_deadline' => 'When do we need to report?',
    'notification_duration' => 'Estimated Fix Duration',
    'notification_duration_placeholder' => 'E.g. 3-5 business days',
    'notification_description' => 'What needs to be fixed / improved?',
    'notification_description_placeholder' => 'Description for the product team...',

    // Footer
    'filled_by' => 'Filled by',
    'filled_by_placeholder' => 'TL name',
    'reviewed_by' => 'Reviewed by (Manager)',
    'reviewed_by_placeholder' => 'Manager name',
    'version' => 'Version',

    // History
    'history_title' => 'History: :name',
    'versions_count' => 'Versions (:count)',
    'snapshot_detail' => 'Snapshot Details',
    'close' => 'Close',
    'no_history' => 'No recorded history.',
    'select_version' => 'Select a version from the list',
    'select_version_hint' => 'Click on a version from the left panel to view details.',

    // Table headers
    'header_project' => 'Project',
    'header_client' => 'Client',
    'header_type' => 'Type',
    'header_phase' => 'Phase',
    'header_health' => 'Health',
    'header_go_live' => 'Go-Live',

    // Phase keys (for current_phase dropdown)
    'phases' => [
        'instalacija_analiza' => 'Installation & Analysis',
        'funkcionalna_specifikacija' => 'Functional Specification',
        'implementacija_testiranje' => 'Implementation & Testing',
        'integracije' => 'Integrations',
        'uat_edukacija' => 'UAT & Training',
        'go_live' => 'Go-Live',
        'hypercare' => 'Hypercare',
    ],

    // Default phase content (for new project form)
    'default_phases' => [
        'instalacija_analiza' => [
            'name' => 'Installation & Requirements Analysis',
            'activities' => 'On-prem setup, requirements gathering, gap analysis',
            'client' => 'Yes',
        ],
        'funkcionalna_specifikacija' => [
            'name' => 'Functional Specification',
            'activities' => 'FuncSpec creation, review, client sign-off',
            'client' => 'Yes - sign-off required',
        ],
        'implementacija_testiranje' => [
            'name' => 'Implementation & Parametrization',
            'activities' => 'System configuration, internal testing, bugfixes',
            'client' => '-',
        ],
        'integracije' => [
            'name' => 'Integrations',
            'activities' => 'Integration development and testing, internal QA',
            'client' => 'Yes - internal sign-off',
        ],
        'uat_edukacija' => [
            'name' => 'UAT & Training',
            'activities' => 'UAT scenarios, user training, adoption activities',
            'client' => 'Yes - UAT sign-off',
        ],
        'go_live' => [
            'name' => 'Go-Live',
            'activities' => 'Production release, go-live checklist',
            'client' => 'Yes',
        ],
        'hypercare' => [
            'name' => 'Hypercare',
            'activities' => 'Post go-live support, project closure',
            'client' => 'Yes - project closure',
        ],
    ],

    // Tabs
    'tab_basic' => 'Basic Info',
    'tab_phases' => 'Phases',
    'tab_estimation' => 'Estimation',
    'tab_risks' => 'Risks & Notifications',
    'tab_burndown' => 'Burndown',

    // Burndown
    'burndown_title' => 'Burndown Chart',
    'burndown_no_data' => 'Not enough data points yet. Save the project a few times to see the burndown chart.',

    // View modes
    'view_list' => 'Projects',
    'view_kanban' => 'Kanban',
    'view_timeline' => 'Timeline',

    // Kanban drag & drop
    'kanban_drag_hint' => 'Drag cards between columns to change health status',

    // Timeline zoom
    'zoom_in' => 'Zoom in',
    'zoom_out' => 'Zoom out',
    'zoom_quarters' => 'Quarters',
    'zoom_months' => 'Months',
    'zoom_weeks' => 'Weeks',

    // Comments
    'comments_title' => 'Comments',
    'comment_placeholder' => 'Write a comment...',
    'comment_add' => 'Add Comment',
    'no_comments' => 'No comments yet.',
    'comment_delete' => 'Delete',

    // Snapshot diff
    'compare' => 'Compare',
    'diff_title' => 'Changes between versions',
    'diff_field' => 'Field',
    'diff_before' => 'Before',
    'diff_after' => 'After',
    'no_changes' => 'No changes detected.',

    // Inline edit
    'inline_edit' => 'Quick edit',

    // Export
    'export_excel' => 'Excel',
    'export_csv' => 'CSV',

    // Snapshot notes
    'snapshot_initial' => 'Initial entry',
    'snapshot_weekly' => 'Weekly update',
    'snapshot_steering' => 'Update after steering committee',
    'snapshot_review' => 'Status review',
    'snapshot_sprint' => 'Post-sprint update',

    // Seeder: phase notes
    'phase_note_blocked' => 'Waiting for client response',
    'phase_note_done' => 'Completed on time',

    // Accessibility (aria-labels)
    'aria_project_type' => 'Project type',
    'aria_phase_name' => 'Phase name',
    'aria_phase_activities' => 'Key activities',
    'aria_phase_client' => 'Client confirmation',
    'aria_phase_status' => 'Phase status',
    'aria_phase_date' => 'Completion date',
    'aria_phase_notes' => 'Phase notes',
    'aria_remove_phase' => 'Remove phase',
    'aria_remove_step' => 'Remove step',
    'aria_remove_risk' => 'Remove risk',
    'aria_step_completed' => 'Step completed',
    'aria_risk_description' => 'Risk description',
    'aria_risk_level' => 'Risk level',
    'aria_risk_mitigation' => 'Risk mitigation',
    'aria_search_projects' => 'Search projects',
    'aria_filter_health' => 'Filter by health status',
    'aria_filter_type' => 'Filter by project type',
    'aria_inline_edit_phase' => 'Change current phase',
    'aria_inline_edit_health' => 'Change health status',
    'aria_close' => 'Close',
    'aria_comment' => 'Write a comment',
    'aria_section_nav' => 'Navigate to section',
    'aria_notification_deadline' => 'Notification deadline',
    'aria_notification_duration' => 'Estimated fix duration',
    'aria_notification_description' => 'Notification description',
    'aria_estimated_hours' => 'Estimated hours',
    'aria_spent_hours' => 'Spent hours',
    'aria_remaining_hours' => 'Remaining hours',
    'aria_estimation_comment' => 'Estimation comment',
    'aria_step_description' => 'Step description',
];
