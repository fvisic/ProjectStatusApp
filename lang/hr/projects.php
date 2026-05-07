<?php

return [
    // Navigation & general
    'title' => 'Projekti',
    'title_list' => 'Projekti - Lista',
    'title_kanban' => 'Projekti - Kanban',
    'title_timeline' => 'Projekti - Vremenska crta',
    'new_project' => '+ Novi projekt',
    'edit_project' => 'Uredi projekt',
    'view_project' => 'Pregled projekta',
    'read_only' => 'Samo pregled',
    'read_only_notice' => 'Gledate ovaj projekt u načinu samo za čitanje. Možete uređivati samo projekte koje ste vi kreirali.',
    'view' => 'Pregled',
    'stale_tooltip' => 'Status nije ažuriran 14+ dana',
    'legend_overdue' => 'Go-live je prošao',
    'legend_soon' => 'Go-live unutar 14 dana',
    'legend_stale' => 'Nema ažuriranja 14+ dana',
    'back_to_list' => 'Natrag na popis',
    'back' => 'Natrag',
    'save' => 'Spremi projekt',
    'saving' => 'Spremam...',
    'cancel' => 'Odustani',
    'delete' => 'Obriši',
    'edit' => 'Uredi',
    'history' => 'Povijest',
    'pdf' => 'PDF',
    'actions' => 'Akcije',
    'no_projects' => 'Nema projekata',
    'no_projects_hint' => 'Kliknite "Novi projekt" za kreiranje prvog projekta.',
    'saved_successfully' => 'Projekt uspješno spremljen.',
    'saved_no_changes' => 'Nema promjena - verzija nije podignuta.',
    'snapshot_changes' => 'Ažurirano',
    'confirm_delete' => 'Jeste li sigurni da želite obrisati ovaj projekt?',

    // Header
    'status_report' => 'Project Status Report',
    'report_subtitle' => 'TL popunjava - ažurira se tjedno ili na zahtjev',

    // Search & filters
    'search_placeholder' => 'Pretraži projekte...',
    'all_statuses' => 'Svi statusi',
    'all_types' => 'Svi tipovi',

    // Meta fields
    'project_name' => 'Projekt',
    'project_name_placeholder' => 'Naziv projekta',
    'client' => 'Klijent',
    'client_placeholder' => 'Naziv klijenta',
    'team_lead' => 'Voditelj projekta',
    'team_lead_placeholder' => 'Ime voditelja projekta',
    'report_date' => 'Datum izvještaja',
    'project_start' => 'Početak projekta',
    'planned_go_live' => 'Planirani Go-Live',
    'current_phase' => 'Trenutna faza',
    'overall_health' => 'Ukupni health',
    'go_live' => 'Go-Live',

    // Project types
    'type' => 'Tip',
    'type_placeholder' => '— odaberi tip —',
    'type_new' => 'New Implementation',
    'type_migration' => 'Migration',
    'type_cr' => 'CR / Modifikacija',

    // Health statuses
    'health_on_track' => 'On Track',
    'health_at_risk' => 'At Risk',
    'health_off_track' => 'Off Track',

    // Phases section
    'phases_title' => 'Faze projekta & Status',
    'phase' => 'Faza',
    'key_activities' => 'Ključne aktivnosti',
    'client_confirmation' => 'Klijent?',
    'status' => 'Status',
    'date' => 'Datum',
    'completion_date' => 'Datum završetka / est.',
    'notes' => 'Napomena',

    // Phase statuses
    'status_pending' => 'Pending',
    'status_in_progress' => 'In Progress',
    'status_done' => 'Done',
    'status_blocked' => 'Blocked',

    // Phase names
    'phase_instalacija' => 'Instalacija & Analiza',
    'phase_funkcionalna' => 'Funkcionalna specifikacija',
    'phase_implementacija' => 'Implementacija & Testiranje',
    'phase_integracije' => 'Integracije',
    'phase_uat' => 'UAT & Edukacija',
    'phase_golive' => 'Go-Live',
    'phase_hypercare' => 'Hypercare',

    // Estimation section
    'estimation_title' => 'Profitabilnost & Estimacija',
    'estimated_hours' => 'Estimirani sati',
    'spent_hours' => 'Utrošeni sati',
    'remaining_hours' => 'Preostalo (est.)',
    'enter_hours' => 'Unesi sate za prikaz',
    'estimation_comment' => 'Komentar uz estimaciju',
    'estimation_comment_placeholder' => 'Gdje smo fulali? Što je uzrok devijacije?',
    'delta_ok' => 'U estimaciji (forecast: :forecasth, :pct% ispod)',
    'delta_warn' => 'Blagi prekorak: +:deltah (+:pct%) iznad estimacije',
    'delta_over' => 'Prekoračenje: +:deltah (+:pct%) - potrebna eskalacija',

    // Next steps
    'next_steps_title' => 'Sljedeći koraci',
    'step_placeholder' => 'Korak :number...',
    'add_step' => '+ Dodaj korak',

    // Risks
    'risks_title' => 'Rizici & Mitigacija',
    'risk_description' => 'Opis rizika',
    'risk_level' => 'Razina',
    'risk_mitigation' => 'Mitigacija',
    'risk_description_placeholder' => 'Npr. klijent kasni sa sign-offom',
    'risk_mitigation_placeholder' => 'Npr. eskalacija na steering committee',
    'add_risk' => '+ Dodaj rizik',
    'add_phase' => '+ Dodaj fazu',
    'remove_phase' => 'Ukloni fazu',
    'drag_to_reorder' => 'Povuci za promjenu redoslijeda',
    'phase_name_placeholder' => 'Naziv faze',
    'phase_activities_placeholder' => 'Ključne aktivnosti',
    'phase_client_placeholder' => 'Da / Ne / -',
    'level_low' => 'Low',
    'level_medium' => 'Medium',
    'level_high' => 'High',

    // Product notification
    'notification_title' => 'Notifikacija Produktu',
    'notification_deadline' => 'Do kada trebamo javiti?',
    'notification_duration' => 'Procijenjeno trajanje popravka',
    'notification_duration_placeholder' => 'Npr. 3-5 radnih dana',
    'notification_description' => 'Što treba biti popravljeno / dorađeno?',
    'notification_description_placeholder' => 'Opis stavki za produkt tim...',

    // Footer
    'filled_by' => 'Popunio/la',
    'filled_by_placeholder' => 'Ime TL-a',
    'reviewed_by' => 'Pregledao/la (Manager)',
    'reviewed_by_placeholder' => 'Ime managera',
    'version' => 'Verzija',

    // History
    'history_title' => 'Povijest: :name',
    'versions_count' => 'Verzije (:count)',
    'snapshot_detail' => 'Detalji snimke',
    'close' => 'Zatvori',
    'no_history' => 'Nema zabilježene povijesti.',
    'select_version' => 'Odaberite verziju iz popisa',
    'select_version_hint' => 'Kliknite na jednu od verzija s lijeve strane za pregled detalja.',

    // Table headers
    'header_project' => 'Projekt',
    'header_client' => 'Klijent',
    'header_type' => 'Tip',
    'header_phase' => 'Faza',
    'header_health' => 'Health',
    'header_go_live' => 'Go-Live',

    // Phase keys (for current_phase dropdown)
    'phases' => [
        'instalacija_analiza' => 'Instalacija & Analiza',
        'funkcionalna_specifikacija' => 'Funkcionalna specifikacija',
        'implementacija_testiranje' => 'Implementacija & Testiranje',
        'integracije' => 'Integracije',
        'uat_edukacija' => 'UAT & Edukacija',
        'go_live' => 'Go-Live',
        'hypercare' => 'Hypercare',
    ],

    // Default phase content (for new project form)
    'default_phases' => [
        'instalacija_analiza' => [
            'name' => 'Instalacija & Analiza zahtjeva',
            'activities' => 'On-prem setup, prikupljanje zahtjeva, gap analiza',
            'client' => 'Da',
        ],
        'funkcionalna_specifikacija' => [
            'name' => 'Funkcionalna specifikacija',
            'activities' => 'Izrada FuncSpec, review, sign-off klijenta',
            'client' => 'Da - sign-off obavezan',
        ],
        'implementacija_testiranje' => [
            'name' => 'Implementacija & Parametrizacija',
            'activities' => 'Konfiguracija sustava, interna testiranja, bugfixevi',
            'client' => '-',
        ],
        'integracije' => [
            'name' => 'Integracije',
            'activities' => 'Razvoj i testiranje integracija, interno QA',
            'client' => 'Da - interni sign-off',
        ],
        'uat_edukacija' => [
            'name' => 'UAT & Edukacija',
            'activities' => 'UAT scenariji, edukacija korisnika, adoption aktivnosti',
            'client' => 'Da - UAT sign-off',
        ],
        'go_live' => [
            'name' => 'Go-Live',
            'activities' => 'Produkcijsko puštanje, go-live checklist',
            'client' => 'Da',
        ],
        'hypercare' => [
            'name' => 'Hypercare',
            'activities' => 'Podrška post go-live, zatvaranje projekta',
            'client' => 'Da - project closure',
        ],
    ],

    // Tabs
    'tab_basic' => 'Osnovno',
    'tab_phases' => 'Faze',
    'tab_estimation' => 'Estimacija',
    'tab_risks' => 'Rizici & Obavijesti',
    'tab_burndown' => 'Burndown',

    // Burndown
    'burndown_title' => 'Burndown grafikon',
    'burndown_no_data' => 'Nedovoljno podataka. Spremite projekt nekoliko puta da biste vidjeli burndown grafikon.',

    // View modes
    'view_list' => 'Projekti',
    'view_kanban' => 'Kanban',
    'view_timeline' => 'Vremenska crta',

    // Kanban drag & drop
    'kanban_drag_hint' => 'Povucite kartice između stupaca za promjenu statusa',

    // Timeline zoom
    'zoom_in' => 'Povećaj',
    'zoom_out' => 'Smanji',
    'zoom_quarters' => 'Kvartali',
    'zoom_months' => 'Mjeseci',
    'zoom_weeks' => 'Tjedni',

    // Comments
    'comments_title' => 'Komentari',
    'comment_placeholder' => 'Napiši komentar...',
    'comment_add' => 'Dodaj komentar',
    'no_comments' => 'Nema komentara.',
    'comment_delete' => 'Obriši',

    // Snapshot diff
    'compare' => 'Usporedi',
    'diff_title' => 'Promjene između verzija',
    'diff_field' => 'Polje',
    'diff_before' => 'Prije',
    'diff_after' => 'Poslije',
    'no_changes' => 'Nema detektiranih promjena.',

    // Inline edit
    'inline_edit' => 'Brza izmjena',

    // Export
    'export_excel' => 'Excel',
    'export_csv' => 'CSV',

    // Snapshot notes
    'snapshot_initial' => 'Inicijalni unos',
    'snapshot_weekly' => 'Tjedni update',
    'snapshot_steering' => 'Update nakon steering commiteeja',
    'snapshot_review' => 'Status review',
    'snapshot_sprint' => 'Post-sprint update',

    // Seeder: phase notes
    'phase_note_blocked' => 'Čekamo odgovor klijenta',
    'phase_note_done' => 'Završeno u roku',

    // Accessibility (aria-labels)
    'aria_project_type' => 'Tip projekta',
    'aria_phase_name' => 'Naziv faze',
    'aria_phase_activities' => 'Ključne aktivnosti',
    'aria_phase_client' => 'Potvrda klijenta',
    'aria_phase_status' => 'Status faze',
    'aria_phase_date' => 'Datum završetka',
    'aria_phase_notes' => 'Napomene uz fazu',
    'aria_remove_phase' => 'Ukloni fazu',
    'aria_remove_step' => 'Ukloni korak',
    'aria_remove_risk' => 'Ukloni rizik',
    'aria_step_completed' => 'Korak dovršen',
    'aria_risk_description' => 'Opis rizika',
    'aria_risk_level' => 'Razina rizika',
    'aria_risk_mitigation' => 'Mitigacija rizika',
    'aria_search_projects' => 'Pretraži projekte',
    'aria_filter_health' => 'Filtriraj po statusu',
    'aria_filter_type' => 'Filtriraj po tipu projekta',
    'aria_inline_edit_phase' => 'Promijeni trenutnu fazu',
    'aria_inline_edit_health' => 'Promijeni status',
    'aria_close' => 'Zatvori',
    'aria_comment' => 'Napišite komentar',
    'aria_section_nav' => 'Navigacija na odjeljak',
    'aria_notification_deadline' => 'Rok obavijesti',
    'aria_notification_duration' => 'Procijenjeno trajanje popravka',
    'aria_notification_description' => 'Opis obavijesti',
    'aria_estimated_hours' => 'Estimirani sati',
    'aria_spent_hours' => 'Utrošeni sati',
    'aria_remaining_hours' => 'Preostali sati',
    'aria_estimation_comment' => 'Komentar uz estimaciju',
    'aria_step_description' => 'Opis koraka',
];
