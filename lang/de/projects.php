<?php

return [
    // Navigation & general
    'title' => 'Projekte',
    'title_list' => 'Projekte - Liste',
    'title_kanban' => 'Projekte - Kanban',
    'title_timeline' => 'Projekte - Zeitstrahl',
    'new_project' => '+ Neues Projekt',
    'edit_project' => 'Projekt bearbeiten',
    'view_project' => 'Projekt ansehen',
    'read_only' => 'Nur-Lesen',
    'read_only_notice' => 'Sie betrachten dieses Projekt im Nur-Lesen-Modus. Sie können nur Projekte bearbeiten, die Sie erstellt haben.',
    'view' => 'Ansehen',
    'stale_tooltip' => 'Kein Status-Update seit 14+ Tagen',
    'legend_overdue' => 'Go-Live-Datum ist überschritten',
    'legend_soon' => 'Go-Live innerhalb von 14 Tagen',
    'legend_stale' => 'Kein Update seit 14+ Tagen',
    'back_to_list' => 'Zurück zur Liste',
    'back' => 'Zurück',
    'save' => 'Projekt speichern',
    'saving' => 'Wird gespeichert...',
    'cancel' => 'Abbrechen',
    'delete' => 'Löschen',
    'edit' => 'Bearbeiten',
    'history' => 'Verlauf',
    'pdf' => 'PDF',
    'actions' => 'Aktionen',
    'no_projects' => 'Keine Projekte',
    'no_projects_hint' => 'Klicken Sie auf "Neues Projekt", um Ihr erstes Projekt zu erstellen.',
    'saved_successfully' => 'Projekt erfolgreich gespeichert.',
    'saved_no_changes' => 'Keine Änderungen erkannt - Version nicht erhöht.',
    'snapshot_changes' => 'Aktualisiert',
    'confirm_delete' => 'Sind Sie sicher, dass Sie dieses Projekt löschen möchten?',

    // Header
    'status_report' => 'Projektstatusbericht',
    'report_subtitle' => 'Vom TL ausgefüllt - wöchentlich oder auf Anfrage aktualisiert',

    // Search & filters
    'search_placeholder' => 'Projekte suchen...',
    'all_statuses' => 'Alle Status',
    'all_types' => 'Alle Typen',

    // Meta fields
    'project_name' => 'Projekt',
    'project_name_placeholder' => 'Projektname',
    'client' => 'Kunde',
    'client_placeholder' => 'Kundenname',
    'team_lead' => 'Projektleiter',
    'team_lead_placeholder' => 'Name des Projektleiters',
    'report_date' => 'Berichtsdatum',
    'project_start' => 'Projektstart',
    'planned_go_live' => 'Geplanter Go-Live',
    'current_phase' => 'Aktuelle Phase',
    'overall_health' => 'Gesamtstatus',
    'go_live' => 'Go-Live',

    // Project types
    'type' => 'Typ',
    'type_placeholder' => '— Typ auswählen —',
    'type_new' => 'Neuimplementierung',
    'type_migration' => 'Migration',
    'type_cr' => 'CR / Änderung',

    // Health statuses
    'health_on_track' => 'Im Plan',
    'health_at_risk' => 'Gefährdet',
    'health_off_track' => 'Außer Plan',

    // Phases section
    'phases_title' => 'Projektphasen & Status',
    'phase' => 'Phase',
    'key_activities' => 'Hauptaktivitäten',
    'client_confirmation' => 'Kunde?',
    'status' => 'Status',
    'date' => 'Datum',
    'completion_date' => 'Abschlussdatum / gesch.',
    'notes' => 'Anmerkungen',

    // Phase statuses
    'status_pending' => 'Ausstehend',
    'status_in_progress' => 'In Bearbeitung',
    'status_done' => 'Erledigt',
    'status_blocked' => 'Blockiert',

    // Phase names
    'phase_instalacija' => 'Installation & Analyse',
    'phase_funkcionalna' => 'Funktionale Spezifikation',
    'phase_implementacija' => 'Implementierung & Test',
    'phase_integracije' => 'Integrationen',
    'phase_uat' => 'UAT & Schulung',
    'phase_golive' => 'Go-Live',
    'phase_hypercare' => 'Hypercare',

    // Estimation section
    'estimation_title' => 'Rentabilität & Schätzung',
    'estimated_hours' => 'Geschätzte Stunden',
    'spent_hours' => 'Verbrauchte Stunden',
    'remaining_hours' => 'Verbleibend (gesch.)',
    'enter_hours' => 'Stunden eingeben zur Anzeige',
    'estimation_comment' => 'Kommentar zur Schätzung',
    'estimation_comment_placeholder' => 'Wo haben wir daneben gelegen? Was hat die Abweichung verursacht?',
    'delta_ok' => 'Im Rahmen der Schätzung (Prognose: :forecasth, :pct% darunter)',
    'delta_warn' => 'Leichte Überschreitung: +:deltah (+:pct%) über Schätzung',
    'delta_over' => 'Überschreitung: +:deltah (+:pct%) - Eskalation erforderlich',

    // Next steps
    'next_steps_title' => 'Nächste Schritte',
    'step_placeholder' => 'Schritt :number...',
    'add_step' => '+ Schritt hinzufügen',

    // Risks
    'risks_title' => 'Risiken & Maßnahmen',
    'risk_description' => 'Risikobeschreibung',
    'risk_level' => 'Stufe',
    'risk_mitigation' => 'Maßnahme',
    'risk_description_placeholder' => 'z. B. Kunde verzögert die Freigabe',
    'risk_mitigation_placeholder' => 'z. B. Eskalation an das Steering Committee',
    'add_risk' => '+ Risiko hinzufügen',
    'add_phase' => '+ Phase hinzufügen',
    'remove_phase' => 'Phase entfernen',
    'drag_to_reorder' => 'Ziehen zum Umsortieren',
    'phase_name_placeholder' => 'Phasenname',
    'phase_activities_placeholder' => 'Hauptaktivitäten',
    'phase_client_placeholder' => 'Ja / Nein / -',
    'level_low' => 'Niedrig',
    'level_medium' => 'Mittel',
    'level_high' => 'Hoch',

    // Product notification
    'notification_title' => 'Produktbenachrichtigung',
    'notification_deadline' => 'Bis wann müssen wir melden?',
    'notification_duration' => 'Geschätzte Behebungsdauer',
    'notification_duration_placeholder' => 'z. B. 3-5 Werktage',
    'notification_description' => 'Was muss behoben / verbessert werden?',
    'notification_description_placeholder' => 'Beschreibung für das Produktteam...',

    // Footer
    'filled_by' => 'Ausgefüllt von',
    'filled_by_placeholder' => 'Name des TL',
    'reviewed_by' => 'Geprüft von (Manager)',
    'reviewed_by_placeholder' => 'Name des Managers',
    'version' => 'Version',

    // History
    'history_title' => 'Verlauf: :name',
    'versions_count' => 'Versionen (:count)',
    'snapshot_detail' => 'Snapshot-Details',
    'close' => 'Schließen',
    'no_history' => 'Kein Verlauf vorhanden.',
    'select_version' => 'Wählen Sie eine Version aus der Liste',
    'select_version_hint' => 'Klicken Sie auf eine Version im linken Bereich, um Details anzuzeigen.',

    // Table headers
    'header_project' => 'Projekt',
    'header_client' => 'Kunde',
    'header_type' => 'Typ',
    'header_phase' => 'Phase',
    'header_health' => 'Status',
    'header_go_live' => 'Go-Live',

    // Phase keys (for current_phase dropdown)
    'phases' => [
        'instalacija_analiza' => 'Installation & Analyse',
        'funkcionalna_specifikacija' => 'Funktionale Spezifikation',
        'implementacija_testiranje' => 'Implementierung & Test',
        'integracije' => 'Integrationen',
        'uat_edukacija' => 'UAT & Schulung',
        'go_live' => 'Go-Live',
        'hypercare' => 'Hypercare',
    ],

    // Default phase content (for new project form)
    'default_phases' => [
        'instalacija_analiza' => [
            'name' => 'Installation & Anforderungsanalyse',
            'activities' => 'On-Prem-Setup, Anforderungserhebung, Gap-Analyse',
            'client' => 'Ja',
        ],
        'funkcionalna_specifikacija' => [
            'name' => 'Funktionale Spezifikation',
            'activities' => 'Erstellung FuncSpec, Review, Kundenfreigabe',
            'client' => 'Ja - Freigabe erforderlich',
        ],
        'implementacija_testiranje' => [
            'name' => 'Implementierung & Parametrisierung',
            'activities' => 'Systemkonfiguration, interne Tests, Fehlerbehebung',
            'client' => '-',
        ],
        'integracije' => [
            'name' => 'Integrationen',
            'activities' => 'Integrationsentwicklung und -test, internes QA',
            'client' => 'Ja - interne Freigabe',
        ],
        'uat_edukacija' => [
            'name' => 'UAT & Schulung',
            'activities' => 'UAT-Szenarien, Anwenderschulung, Adoptionsaktivitäten',
            'client' => 'Ja - UAT-Freigabe',
        ],
        'go_live' => [
            'name' => 'Go-Live',
            'activities' => 'Produktivrelease, Go-Live-Checkliste',
            'client' => 'Ja',
        ],
        'hypercare' => [
            'name' => 'Hypercare',
            'activities' => 'Support nach Go-Live, Projektabschluss',
            'client' => 'Ja - Projektabschluss',
        ],
    ],

    // Tabs
    'tab_basic' => 'Grunddaten',
    'tab_phases' => 'Phasen',
    'tab_estimation' => 'Schätzung',
    'tab_risks' => 'Risiken & Benachrichtigungen',
    'tab_burndown' => 'Burndown',

    // Burndown
    'burndown_title' => 'Burndown-Diagramm',
    'burndown_no_data' => 'Noch nicht genügend Datenpunkte vorhanden. Speichern Sie das Projekt mehrmals, um das Burndown-Diagramm anzuzeigen.',

    // View modes
    'view_list' => 'Projekte',
    'view_kanban' => 'Kanban',
    'view_timeline' => 'Zeitstrahl',

    // Kanban drag & drop
    'kanban_drag_hint' => 'Karten zwischen Spalten ziehen, um den Status zu ändern',

    // Timeline zoom
    'zoom_in' => 'Vergrößern',
    'zoom_out' => 'Verkleinern',
    'zoom_quarters' => 'Quartale',
    'zoom_months' => 'Monate',
    'zoom_weeks' => 'Wochen',

    // Comments
    'comments_title' => 'Kommentare',
    'comment_placeholder' => 'Kommentar schreiben...',
    'comment_add' => 'Kommentar hinzufügen',
    'no_comments' => 'Noch keine Kommentare.',
    'comment_delete' => 'Löschen',

    // Snapshot diff
    'compare' => 'Vergleichen',
    'diff_title' => 'Änderungen zwischen Versionen',
    'diff_field' => 'Feld',
    'diff_before' => 'Vorher',
    'diff_after' => 'Nachher',
    'no_changes' => 'Keine Änderungen erkannt.',

    // Inline edit
    'inline_edit' => 'Schnellbearbeitung',

    // Export
    'export_excel' => 'Excel',
    'export_csv' => 'CSV',

    // Snapshot notes
    'snapshot_initial' => 'Ersterfassung',
    'snapshot_weekly' => 'Wöchentliches Update',
    'snapshot_steering' => 'Update nach Steering Committee',
    'snapshot_review' => 'Statusüberprüfung',
    'snapshot_sprint' => 'Post-Sprint-Update',

    // Seeder: phase notes
    'phase_note_blocked' => 'Warten auf Kundenrückmeldung',
    'phase_note_done' => 'Termingerecht abgeschlossen',

    // Accessibility (aria-labels)
    'aria_project_type' => 'Projekttyp',
    'aria_phase_name' => 'Phasenname',
    'aria_phase_activities' => 'Hauptaktivitäten',
    'aria_phase_client' => 'Kundenbestätigung',
    'aria_phase_status' => 'Phasenstatus',
    'aria_phase_date' => 'Abschlussdatum',
    'aria_phase_notes' => 'Anmerkungen zur Phase',
    'aria_remove_phase' => 'Phase entfernen',
    'aria_remove_step' => 'Schritt entfernen',
    'aria_remove_risk' => 'Risiko entfernen',
    'aria_step_completed' => 'Schritt abgeschlossen',
    'aria_risk_description' => 'Risikobeschreibung',
    'aria_risk_level' => 'Risikostufe',
    'aria_risk_mitigation' => 'Risikomaßnahme',
    'aria_search_projects' => 'Projekte suchen',
    'aria_filter_health' => 'Nach Status filtern',
    'aria_filter_type' => 'Nach Projekttyp filtern',
    'aria_inline_edit_phase' => 'Aktuelle Phase ändern',
    'aria_inline_edit_health' => 'Statusänderung',
    'aria_close' => 'Schließen',
    'aria_comment' => 'Kommentar schreiben',
    'aria_section_nav' => 'Zum Abschnitt navigieren',
    'aria_notification_deadline' => 'Meldefrist',
    'aria_notification_duration' => 'Geschätzte Behebungsdauer',
    'aria_notification_description' => 'Beschreibung der Benachrichtigung',
    'aria_estimated_hours' => 'Geschätzte Stunden',
    'aria_spent_hours' => 'Verbrauchte Stunden',
    'aria_remaining_hours' => 'Verbleibende Stunden',
    'aria_estimation_comment' => 'Kommentar zur Schätzung',
    'aria_step_description' => 'Schrittbeschreibung',
];
