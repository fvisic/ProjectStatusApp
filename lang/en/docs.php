<?php

return [
    'nav' => 'Docs',
    'title' => 'Documentation',
    'restart_tutorial' => 'Restart Tutorial',

    // Sidebar
    'nav_overview' => 'Overview',
    'nav_projects' => 'Projects',
    'nav_views' => 'Views',
    'nav_dashboard' => 'Dashboard',
    'nav_exports' => 'Exports & PDF',
    'nav_notifications' => 'Notifications',
    'nav_security' => 'Security',
    'nav_settings' => 'Settings',
    'nav_faq' => 'FAQ',

    // Security
    'security_title' => 'Security',
    'security_intro' => 'ProjectStatus offers multiple layers of account security: role-based access control, two-factor authentication (TOTP), passkeys (WebAuthn), and admin tools to manage users.',
    'security_roles_title' => 'Roles',
    'security_roles_intro' => 'Every user has exactly one role. Roles control what they can see and do across the app.',
    'security_role_admin_desc' => 'Full access. Can view, edit, delete every project. Can manage users, change roles, reset passwords, disable 2FA/passkeys, impersonate other users.',
    'security_role_manager_desc' => 'Read-only access to all projects and reports across the portfolio. Can edit only their own projects.',
    'security_role_user_desc' => 'Sees and edits only the projects they own.',

    'security_2fa_title' => 'Two-factor authentication (TOTP)',
    'security_2fa_intro' => 'Add a one-time code from an authenticator app to every login. Recommended for all users with sensitive access.',
    'security_2fa_s1' => 'Open Profile → Two-Factor Authentication and click Enable.',
    'security_2fa_s2' => 'Scan the QR code with Google Authenticator, 1Password, Authy, or any TOTP app.',
    'security_2fa_s3' => 'Enter the 6-digit code shown by the app to confirm.',
    'security_2fa_s4' => 'Save the recovery codes shown - each can be used once if you lose your authenticator.',
    'security_2fa_recovery_tip' => 'Recovery codes are single-use. Store them in a password manager. After using one, generate new codes from the same screen.',

    'security_passkeys_title' => 'Passkeys (WebAuthn)',
    'security_passkeys_intro' => 'Sign in with Touch ID, Face ID, Windows Hello, or a hardware security key (YubiKey). No password to type. You can register multiple devices.',
    'security_passkeys_s1' => 'Open Profile → Passkeys.',
    'security_passkeys_s2' => 'Type a name for the device (e.g. "MacBook Touch ID") and click Add passkey.',
    'security_passkeys_s3' => 'Confirm with your fingerprint, face, PIN, or hardware key when the browser prompts.',
    'security_passkeys_s4' => 'On the login screen, click Sign in with passkey instead of typing a password.',
    'security_passkeys_hybrid_tip' => 'Passkeys can coexist with TOTP and password login. You can use whichever is most convenient on each device.',

    'security_users_title' => 'Admin user management',
    'security_users_intro' => 'Admins can manage all users from the Users page (admin only). Per-user actions:',
    'security_users_action_reset_password' => 'Reset password - generates a new temporary password to share with the user.',
    'security_users_action_disable' => 'Disable account - blocks login without deleting data.',
    'security_users_action_disable_2fa' => 'Disable 2FA - clears the user\'s TOTP secret if they lost their authenticator.',
    'security_users_action_reset_passkeys' => 'Reset passkeys - deletes all registered passkeys when the user has lost all their devices.',
    'security_users_action_change_role' => 'Change role - promote or demote between admin / manager / user.',
    'security_users_action_delete' => 'Delete - permanent removal (cannot be undone).',

    'security_impersonation_title' => 'Impersonation',
    'security_impersonation_desc' => 'Admins can sign in as any non-admin user from the Users page. A yellow banner appears at the top while impersonating; click "Stop" to return to your own session. Cannot impersonate another admin or chain impersonation.',

    // Overview
    'overview_title' => 'Project Status - Overview',
    'overview_intro' => 'Project Status is a portfolio management tool designed for team leads and project managers. It helps you track project health, timelines, budgets, risks, and deliverables across your entire portfolio.',
    'feature_tracking' => 'Project Tracking',
    'feature_dashboard' => 'Portfolio Dashboard',
    'feature_reports' => 'Reports & Export',
    'feature_notifications' => 'Notifications',
    'feature_history' => 'Version History',
    'feature_i18n' => 'EN / HR Languages',
    'overview_roles_title' => 'User Roles',
    'role' => 'Role',
    'role_permissions' => 'Permissions',
    'role_user' => 'User',
    'role_user_desc' => 'Create, edit, and delete own projects. View own dashboard metrics.',
    'role_admin' => 'Admin',
    'role_admin_desc' => 'All user permissions plus: view all projects across users, receive weekly portfolio reports, manage any project.',

    // Projects
    'projects_title' => 'Working with Projects',
    'projects_intro' => 'Each project tracks a complete implementation lifecycle, from initial analysis through go-live and hypercare.',
    'projects_create_title' => 'Creating a New Project',
    'projects_create_s1' => 'Navigate to Projects → List and click "+ New Project"',
    'projects_create_s2' => 'Fill in the Basic Info tab: name, client, team lead, project type, dates, and health status',
    'projects_create_s3' => 'Configure Phases: set the status and completion date for each of the 7 project phases',
    'projects_create_s4' => 'Save the project. A snapshot is automatically created for version history.',
    'projects_tabs_title' => 'Project Form Tabs',
    'tab_basic_desc' => 'Project metadata - name, client, team lead, type (New/Migration/CR), dates, health status, and next steps.',
    'tab_phases_desc' => '7 implementation phases with status tracking, key activities, client confirmation flags, and notes.',
    'tab_estimation_desc' => 'Budget tracking - estimated hours, spent hours, remaining estimate, forecast delta with automatic alerts.',
    'tab_risks_desc' => 'Risk register with severity levels (Low/Medium/High) and mitigation plans. Product notifications with deadlines.',
    'tab_burndown_desc' => 'Visual burndown chart showing estimated, spent, and remaining hours over time (requires 2+ snapshots).',
    'projects_health_title' => 'Health Status',
    'health_on_track_desc' => 'Project is progressing as planned. No issues or delays.',
    'health_at_risk_desc' => 'Potential issues identified. Close monitoring required.',
    'health_off_track_desc' => 'Significant delays or blockers. Escalation needed.',
    'projects_phases_title' => 'Project Lifecycle',
    'projects_phases_intro' => 'Every project follows a 7-phase lifecycle. Each phase can be tracked with status, dates, and notes:',

    // Views
    'views_title' => 'Project Views',
    'view_list_title' => 'List View',
    'view_list_full_desc' => 'The default view shows all projects in a table with columns for name, client, type, phase, health, and go-live date. Use the search bar to find projects by name or client, and filter by health status or project type.',
    'view_list_tip' => 'Click the pencil icon on any row for inline editing - quickly change health status or phase without opening the full form.',
    'view_kanban_title' => 'Kanban Board',
    'view_kanban_full_desc' => 'The Kanban view organizes projects into three columns by health status: On Track, At Risk, and Off Track. Each card shows project name, client, phase, go-live date, and a budget progress bar.',
    'view_kanban_tip' => 'Drag and drop cards between columns to instantly change a project\'s health status. A snapshot is created automatically.',
    'view_timeline_title' => 'Timeline / Gantt',
    'view_timeline_full_desc' => 'The timeline view shows projects as horizontal bars spanning from project start to planned go-live. Bars are color-coded by health status. A red vertical line marks today\'s date.',
    'view_timeline_tip' => 'Use the zoom controls (magnifying glass icons) to switch between quarterly, monthly, and weekly granularity.',
    'tip' => 'Tip',

    // Dashboard
    'dashboard_title' => 'Dashboard',
    'dashboard_intro' => 'The dashboard provides a real-time overview of your project portfolio with KPI cards, trend charts, and alerts.',
    'dashboard_kpi_title' => 'KPI Cards',
    'dashboard_kpi_desc' => 'The top row shows: total projects, health distribution (On Track / At Risk / Off Track), project type distribution, and phase distribution. Admins see metrics for all users.',
    'dashboard_trends_title' => 'Trend Charts',
    'dashboard_trends_desc' => 'Two 8-week trend charts show: (1) Health trend - stacked bar chart showing how the on-track/at-risk/off-track ratio changes week by week, and (2) Spent hours trend - area chart tracking cumulative hours spent.',
    'dashboard_alerts_title' => 'Alerts',
    'alert_offtrack' => 'Off-track projects that need immediate attention',
    'alert_golive' => 'Projects with go-live dates within the next 30 days',
    'alert_budget' => 'Projects exceeding budget by more than 15%',

    // Exports
    'exports_title' => 'Exports & Reports',
    'exports_intro' => 'Generate professional reports in multiple formats for stakeholders and record-keeping.',
    'export_single_pdf_title' => 'Single Project PDF',
    'export_single_pdf_desc' => 'Click "PDF" in the project list to generate a detailed A4 report including all project metadata, phases, risks, estimation details, and next steps. Perfect for steering committee presentations.',
    'export_portfolio_title' => 'Portfolio PDF',
    'export_portfolio_desc' => 'Click "Portfolio PDF" on the Dashboard to generate a landscape summary of all projects with KPIs, health overview, and individual project cards. Ideal for executive reporting.',
    'export_excel_title' => 'Excel / CSV Export',
    'export_excel_desc' => 'Export the filtered project list as XLSX or CSV from the List view. The export respects your current health/type filters and includes all key fields.',

    // Notifications
    'notifications_title' => 'Notifications',
    'notifications_intro' => 'The app sends automated alerts to keep you informed about project risks and milestones.',
    'notif_daily_title' => 'Daily Alerts (08:00)',
    'notif_daily_desc' => 'Every morning, the system checks for: off-track projects, go-live dates within 7 days, and budget overruns exceeding 15%. Project owners receive email notifications for their affected projects.',
    'notif_weekly_title' => 'Weekly Report (Monday 07:00)',
    'notif_weekly_desc' => 'Admins receive a comprehensive weekly portfolio summary every Monday morning. It includes project counts, health breakdown, estimation statistics, a list of off-track projects, and upcoming go-lives.',
    'notif_webhook_title' => 'Slack / Teams Webhooks',
    'notif_webhook_desc' => 'In addition to email, alerts can be delivered to Slack or Microsoft Teams channels via incoming webhooks.',
    'notif_webhook_setup' => 'Setup:',
    'notif_webhook_s1' => 'Create an Incoming Webhook in your Slack/Teams workspace',
    'notif_webhook_s2' => 'Go to Profile → Slack/Teams Webhook URL and paste the URL',
    'notif_webhook_s3' => 'Alerts will now be sent to both email and your webhook',

    // Settings
    'settings_title' => 'Settings',
    'settings_intro' => 'Personalize your experience from the Profile page.',
    'settings_profile_title' => 'Profile',
    'settings_profile_desc' => 'Update your name, email, and password from the Profile page (accessible via the dropdown in the top-right corner).',
    'settings_language_title' => 'Language',
    'settings_language_desc' => 'Switch between English and Croatian using the language toggle in the navigation bar. Your preference is saved and persists across sessions.',
    'settings_webhook_title' => 'Webhook URL',
    'settings_webhook_desc' => 'Add your Slack or Teams webhook URL in the Profile page to receive project alerts in your team channel.',

    // FAQ
    'faq_title' => 'Frequently Asked Questions',
    'faqs' => [
        [
            'q' => 'How do I change a project\'s health status quickly?',
            'a' => 'Use the Kanban board - drag a card from one column to another. Or click the pencil icon in the List view for inline editing.',
        ],
        [
            'q' => 'Can I see who changed what on a project?',
            'a' => 'Yes. Every save creates a snapshot in the Version History. Click "History" next to a project to see all versions and compare changes between any two.',
        ],
        [
            'q' => 'What triggers an email alert?',
            'a' => 'Three things: (1) A project marked as Off Track, (2) Go-live date within 7 days, (3) Budget overrun exceeding 15%. Alerts are sent daily at 08:00.',
        ],
        [
            'q' => 'How does the burndown chart work?',
            'a' => 'The burndown chart plots estimated, spent, and remaining hours over time. Each project save creates a data point. You need at least 2 saves to see the chart.',
        ],
        [
            'q' => 'Can I export filtered data?',
            'a' => 'Yes. Set your filters (health, type) in the List view first, then click Excel or CSV. The export respects the active filters.',
        ],
        [
            'q' => 'How do I restart the onboarding tutorial?',
            'a' => 'Go to the Docs page and click "Restart Tutorial" in the top right corner.',
        ],
        [
            'q' => 'What\'s the difference between User and Admin roles?',
            'a' => 'Users see only their own projects. Admins see all projects across all users, receive weekly portfolio reports, and can edit any project.',
        ],
        [
            'q' => 'How do I set up Slack notifications?',
            'a' => 'Create an Incoming Webhook in your Slack workspace, then paste the URL in Profile → Slack/Teams Webhook URL. Both daily alerts and weekly reports will be sent there.',
        ],
    ],
];
