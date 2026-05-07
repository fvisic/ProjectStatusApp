<?php

return [
    'nav' => 'Docs',
    'title' => 'Dokumentation',
    'restart_tutorial' => 'Tutorial neu starten',

    // Sidebar
    'nav_overview' => 'Übersicht',
    'nav_projects' => 'Projekte',
    'nav_views' => 'Ansichten',
    'nav_dashboard' => 'Dashboard',
    'nav_exports' => 'Exporte & PDF',
    'nav_notifications' => 'Benachrichtigungen',
    'nav_security' => 'Sicherheit',
    'nav_settings' => 'Einstellungen',
    'nav_faq' => 'FAQ',

    // Security
    'security_title' => 'Sicherheit',
    'security_intro' => 'ProjectStatus bietet mehrere Sicherheitsebenen für Benutzerkonten: rollenbasierte Zugriffskontrolle, Zwei-Faktor-Authentifizierung (TOTP), Passkeys (WebAuthn) und Admin-Tools zur Benutzerverwaltung.',
    'security_roles_title' => 'Rollen',
    'security_roles_intro' => 'Jeder Benutzer hat genau eine Rolle. Rollen steuern, was Benutzer in der App sehen und tun können.',
    'security_role_admin_desc' => 'Voller Zugriff. Kann jedes Projekt sehen, bearbeiten und löschen. Kann Benutzer verwalten, Rollen ändern, Passwörter zurücksetzen, 2FA/Passkeys deaktivieren und sich als andere Benutzer anmelden.',
    'security_role_manager_desc' => 'Lesezugriff auf alle Projekte und Berichte im Portfolio. Kann nur eigene Projekte bearbeiten.',
    'security_role_user_desc' => 'Sieht und bearbeitet nur eigene Projekte.',

    'security_2fa_title' => 'Zwei-Faktor-Authentifizierung (TOTP)',
    'security_2fa_intro' => 'Fügen Sie bei jeder Anmeldung einen Einmalcode aus einer Authenticator-App hinzu. Empfohlen für alle Benutzer mit sensiblem Zugriff.',
    'security_2fa_s1' => 'Öffnen Sie Profil → Zwei-Faktor-Authentifizierung und klicken Sie auf Aktivieren.',
    'security_2fa_s2' => 'Scannen Sie den QR-Code mit Google Authenticator, 1Password, Authy oder einer anderen TOTP-App.',
    'security_2fa_s3' => 'Geben Sie zur Bestätigung den 6-stelligen Code aus der App ein.',
    'security_2fa_s4' => 'Speichern Sie die angezeigten Wiederherstellungscodes - jeder kann einmal verwendet werden, falls Sie Ihren Authenticator verlieren.',
    'security_2fa_recovery_tip' => 'Wiederherstellungscodes sind einmal verwendbar. Speichern Sie sie in einem Passwort-Manager. Nach der Verwendung eines Codes generieren Sie neue über denselben Bildschirm.',

    'security_passkeys_title' => 'Passkeys (WebAuthn)',
    'security_passkeys_intro' => 'Melden Sie sich mit Touch ID, Face ID, Windows Hello oder einem Hardware-Sicherheitsschlüssel (YubiKey) an. Kein Passwort erforderlich. Sie können mehrere Geräte registrieren.',
    'security_passkeys_s1' => 'Öffnen Sie Profil → Passkeys.',
    'security_passkeys_s2' => 'Geben Sie einen Gerätenamen ein (z. B. "MacBook Touch ID") und klicken Sie auf Passkey hinzufügen.',
    'security_passkeys_s3' => 'Bestätigen Sie mit Fingerabdruck, Gesicht, PIN oder Hardware-Schlüssel, wenn der Browser danach fragt.',
    'security_passkeys_s4' => 'Klicken Sie im Anmeldebildschirm auf Mit Passkey anmelden statt ein Passwort einzugeben.',
    'security_passkeys_hybrid_tip' => 'Passkeys können neben TOTP und Passwort-Anmeldung gleichzeitig existieren. Verwenden Sie auf jedem Gerät, was am bequemsten ist.',

    'security_users_title' => 'Admin-Benutzerverwaltung',
    'security_users_intro' => 'Admins können alle Benutzer auf der Benutzer-Seite verwalten (nur Admin). Aktionen pro Benutzer:',
    'security_users_action_reset_password' => 'Passwort zurücksetzen - generiert ein neues temporäres Passwort zum Teilen mit dem Benutzer.',
    'security_users_action_disable' => 'Konto deaktivieren - sperrt die Anmeldung ohne Datenverlust.',
    'security_users_action_disable_2fa' => '2FA deaktivieren - löscht das TOTP-Geheimnis des Benutzers, wenn er den Authenticator verloren hat.',
    'security_users_action_reset_passkeys' => 'Passkeys zurücksetzen - löscht alle registrierten Passkeys, wenn der Benutzer alle Geräte verloren hat.',
    'security_users_action_change_role' => 'Rolle ändern - hochstufen oder herabstufen zwischen Admin / Manager / Benutzer.',
    'security_users_action_delete' => 'Löschen - endgültige Entfernung (nicht rückgängig zu machen).',

    'security_impersonation_title' => 'Identitätsübernahme',
    'security_impersonation_desc' => 'Admins können sich auf der Benutzer-Seite als beliebiger Nicht-Admin-Benutzer anmelden. Während der Übernahme erscheint oben ein gelbes Banner; klicken Sie auf "Stop", um zur eigenen Sitzung zurückzukehren. Andere Admins können nicht übernommen oder Übernahmen verkettet werden.',

    // Overview
    'overview_title' => 'Project Status - Übersicht',
    'overview_intro' => 'Project Status ist ein Portfoliomanagement-Tool für Teamleiter und Projektmanager. Es hilft Ihnen, Projektstatus, Zeitpläne, Budgets, Risiken und Lieferobjekte über Ihr gesamtes Portfolio zu verfolgen.',
    'feature_tracking' => 'Projektverfolgung',
    'feature_dashboard' => 'Portfolio-Dashboard',
    'feature_reports' => 'Berichte & Export',
    'feature_notifications' => 'Benachrichtigungen',
    'feature_history' => 'Versionsverlauf',
    'feature_i18n' => 'EN / HR / DE Sprachen',
    'overview_roles_title' => 'Benutzerrollen',
    'role' => 'Rolle',
    'role_permissions' => 'Berechtigungen',
    'role_user' => 'Benutzer',
    'role_user_desc' => 'Eigene Projekte erstellen, bearbeiten und löschen. Eigene Dashboard-Metriken anzeigen.',
    'role_admin' => 'Admin',
    'role_admin_desc' => 'Alle Benutzerberechtigungen plus: alle Projekte aller Benutzer einsehen, wöchentliche Portfolioberichte erhalten, jedes Projekt verwalten.',

    // Projects
    'projects_title' => 'Mit Projekten arbeiten',
    'projects_intro' => 'Jedes Projekt verfolgt einen vollständigen Implementierungslebenszyklus, von der ersten Analyse bis zum Go-Live und Hypercare.',
    'projects_create_title' => 'Ein neues Projekt erstellen',
    'projects_create_s1' => 'Navigieren Sie zu Projekte → Liste und klicken Sie auf "+ Neues Projekt"',
    'projects_create_s2' => 'Füllen Sie den Tab Grunddaten aus: Name, Kunde, Team Lead, Projekttyp, Termine und Status',
    'projects_create_s3' => 'Konfigurieren Sie die Phasen: Setzen Sie Status und Abschlussdatum für jede der 7 Projektphasen',
    'projects_create_s4' => 'Speichern Sie das Projekt. Ein Snapshot wird automatisch für den Versionsverlauf erstellt.',
    'projects_tabs_title' => 'Projektformular-Tabs',
    'tab_basic_desc' => 'Projektmetadaten - Name, Kunde, TL, Typ (Neu/Migration/CR), Termine, Status und nächste Schritte.',
    'tab_phases_desc' => '7 Implementierungsphasen mit Statusverfolgung, Hauptaktivitäten, Kundenbestätigungskennzeichen und Anmerkungen.',
    'tab_estimation_desc' => 'Budgetverfolgung - geschätzte Stunden, verbrauchte Stunden, Restschätzung, Prognose-Delta mit automatischen Warnungen.',
    'tab_risks_desc' => 'Risikoregister mit Schweregrad-Stufen (Niedrig/Mittel/Hoch) und Maßnahmenplänen. Produktbenachrichtigungen mit Fristen.',
    'tab_burndown_desc' => 'Visuelles Burndown-Diagramm, das geschätzte, verbrauchte und verbleibende Stunden im Zeitverlauf zeigt (mindestens 2 Snapshots erforderlich).',
    'projects_health_title' => 'Projektstatus',
    'health_on_track_desc' => 'Projekt verläuft planmäßig. Keine Probleme oder Verzögerungen.',
    'health_at_risk_desc' => 'Potenzielle Probleme identifiziert. Engmaschige Überwachung erforderlich.',
    'health_off_track_desc' => 'Erhebliche Verzögerungen oder Blockaden. Eskalation erforderlich.',
    'projects_phases_title' => 'Projektlebenszyklus',
    'projects_phases_intro' => 'Jedes Projekt durchläuft einen 7-Phasen-Lebenszyklus. Jede Phase kann mit Status, Terminen und Anmerkungen verfolgt werden:',

    // Views
    'views_title' => 'Projektansichten',
    'view_list_title' => 'Listenansicht',
    'view_list_full_desc' => 'Die Standardansicht zeigt alle Projekte in einer Tabelle mit Spalten für Name, Kunde, Typ, Phase, Status und Go-Live-Datum. Verwenden Sie die Suchleiste, um Projekte nach Name oder Kunde zu finden, und filtern Sie nach Status oder Projekttyp.',
    'view_list_tip' => 'Klicken Sie auf das Stiftsymbol in einer Zeile für die Schnellbearbeitung - ändern Sie Status oder Phase, ohne das vollständige Formular zu öffnen.',
    'view_kanban_title' => 'Kanban-Board',
    'view_kanban_full_desc' => 'Die Kanban-Ansicht ordnet Projekte in drei Spalten nach Status: Im Plan, Gefährdet und Außer Plan. Jede Karte zeigt Projektname, Kunde, Phase, Go-Live-Datum und einen Budget-Fortschrittsbalken.',
    'view_kanban_tip' => 'Ziehen Sie Karten zwischen Spalten, um den Status eines Projekts sofort zu ändern. Ein Snapshot wird automatisch erstellt.',
    'view_timeline_title' => 'Zeitstrahl / Gantt',
    'view_timeline_full_desc' => 'Die Zeitstrahl-Ansicht zeigt Projekte als horizontale Balken vom Projektstart bis zum geplanten Go-Live. Die Balken sind nach Status farbcodiert. Eine rote vertikale Linie markiert das heutige Datum.',
    'view_timeline_tip' => 'Verwenden Sie die Zoom-Steuerung (Lupensymbole), um zwischen Quartals-, Monats- und Wochenansicht zu wechseln.',
    'tip' => 'Tipp',

    // Dashboard
    'dashboard_title' => 'Dashboard',
    'dashboard_intro' => 'Das Dashboard bietet einen Echtzeit-Überblick über Ihr Projektportfolio mit KPI-Karten, Trenddiagrammen und Warnungen.',
    'dashboard_kpi_title' => 'KPI-Karten',
    'dashboard_kpi_desc' => 'Die obere Zeile zeigt: Gesamtprojekte, Statusverteilung (Im Plan / Gefährdet / Außer Plan), Typverteilung und Phasenverteilung. Admins sehen Metriken aller Benutzer.',
    'dashboard_trends_title' => 'Trenddiagramme',
    'dashboard_trends_desc' => 'Zwei 8-Wochen-Trenddiagramme zeigen: (1) Statustrend - gestapeltes Balkendiagramm, das zeigt, wie sich das Verhältnis Im Plan/Gefährdet/Außer Plan wöchentlich ändert, und (2) Verbrauchte Stunden Trend - Flächendiagramm, das die kumulierten verbrauchten Stunden verfolgt.',
    'dashboard_alerts_title' => 'Warnungen',
    'alert_offtrack' => 'Projekte außer Plan, die sofortige Aufmerksamkeit erfordern',
    'alert_golive' => 'Projekte mit Go-Live-Datum innerhalb der nächsten 30 Tage',
    'alert_budget' => 'Projekte, die das Budget um mehr als 15% überschreiten',

    // Exports
    'exports_title' => 'Exporte & Berichte',
    'exports_intro' => 'Erstellen Sie professionelle Berichte in verschiedenen Formaten für Stakeholder und zur Dokumentation.',
    'export_single_pdf_title' => 'Einzelprojekt-PDF',
    'export_single_pdf_desc' => 'Klicken Sie auf "PDF" in der Projektliste, um einen detaillierten A4-Bericht mit allen Projektmetadaten, Phasen, Risiken, Schätzungsdetails und nächsten Schritten zu erstellen. Ideal für Steering-Committee-Präsentationen.',
    'export_portfolio_title' => 'Portfolio-PDF',
    'export_portfolio_desc' => 'Klicken Sie auf "Portfolio-PDF" im Dashboard, um eine Querformat-Zusammenfassung aller Projekte mit KPIs, Statusübersicht und einzelnen Projektkarten zu erstellen. Ideal für das Management-Reporting.',
    'export_excel_title' => 'Excel / CSV-Export',
    'export_excel_desc' => 'Exportieren Sie die gefilterte Projektliste als XLSX oder CSV aus der Listenansicht. Der Export berücksichtigt Ihre aktuellen Status-/Typfilter und enthält alle Schlüsselfelder.',

    // Notifications
    'notifications_title' => 'Benachrichtigungen',
    'notifications_intro' => 'Die Anwendung sendet automatisierte Warnungen, um Sie über Projektrisiken und Meilensteine auf dem Laufenden zu halten.',
    'notif_daily_title' => 'Tägliche Warnungen (08:00)',
    'notif_daily_desc' => 'Jeden Morgen prüft das System: Projekte außer Plan, Go-Live-Termine innerhalb von 7 Tagen und Budgetüberschreitungen von mehr als 15%. Projektverantwortliche erhalten E-Mail-Benachrichtigungen für ihre betroffenen Projekte.',
    'notif_weekly_title' => 'Wöchentlicher Bericht (Montag 07:00)',
    'notif_weekly_desc' => 'Admins erhalten jeden Montagmorgen eine umfassende wöchentliche Portfoliozusammenfassung. Sie umfasst Projektanzahl, Statusverteilung, Schätzungsstatistiken, eine Liste der Projekte außer Plan und anstehende Go-Lives.',
    'notif_webhook_title' => 'Slack / Teams Webhooks',
    'notif_webhook_desc' => 'Zusätzlich zu E-Mail können Warnungen über eingehende Webhooks an Slack- oder Microsoft-Teams-Kanäle gesendet werden.',
    'notif_webhook_setup' => 'Einrichtung:',
    'notif_webhook_s1' => 'Erstellen Sie einen Incoming Webhook in Ihrem Slack/Teams-Workspace',
    'notif_webhook_s2' => 'Gehen Sie zu Profil → Slack/Teams Webhook URL und fügen Sie die URL ein',
    'notif_webhook_s3' => 'Warnungen werden nun sowohl per E-Mail als auch an Ihren Webhook gesendet',

    // Settings
    'settings_title' => 'Einstellungen',
    'settings_intro' => 'Personalisieren Sie Ihre Nutzererfahrung über die Profilseite.',
    'settings_profile_title' => 'Profil',
    'settings_profile_desc' => 'Aktualisieren Sie Ihren Namen, Ihre E-Mail-Adresse und Ihr Passwort über die Profilseite (zugänglich über das Dropdown-Menü oben rechts).',
    'settings_language_title' => 'Sprache',
    'settings_language_desc' => 'Wechseln Sie zwischen Englisch, Kroatisch und Deutsch über den Sprachumschalter in der Navigationsleiste. Ihre Auswahl wird gespeichert und bleibt sitzungsübergreifend erhalten.',
    'settings_webhook_title' => 'Webhook-URL',
    'settings_webhook_desc' => 'Fügen Sie Ihre Slack- oder Teams-Webhook-URL auf der Profilseite hinzu, um Projektwarnungen in Ihrem Teamkanal zu erhalten.',

    // FAQ
    'faq_title' => 'Häufig gestellte Fragen',
    'faqs' => [
        [
            'q' => 'Wie kann ich den Status eines Projekts schnell ändern?',
            'a' => 'Verwenden Sie das Kanban-Board - ziehen Sie eine Karte von einer Spalte in eine andere. Oder klicken Sie auf das Stiftsymbol in der Listenansicht für die Schnellbearbeitung.',
        ],
        [
            'q' => 'Kann ich sehen, wer was an einem Projekt geändert hat?',
            'a' => 'Ja. Jedes Speichern erstellt einen Snapshot im Versionsverlauf. Klicken Sie auf "Verlauf" neben einem Projekt, um alle Versionen anzuzeigen und Änderungen zwischen beliebigen zwei Versionen zu vergleichen.',
        ],
        [
            'q' => 'Was löst eine E-Mail-Warnung aus?',
            'a' => 'Drei Dinge: (1) Ein Projekt, das als Außer Plan markiert ist, (2) Go-Live-Termin innerhalb von 7 Tagen, (3) Budgetüberschreitung von mehr als 15%. Warnungen werden täglich um 08:00 Uhr gesendet.',
        ],
        [
            'q' => 'Wie funktioniert das Burndown-Diagramm?',
            'a' => 'Das Burndown-Diagramm stellt geschätzte, verbrauchte und verbleibende Stunden im Zeitverlauf dar. Jedes Speichern des Projekts erstellt einen Datenpunkt. Sie benötigen mindestens 2 Speichervorgänge, um das Diagramm zu sehen.',
        ],
        [
            'q' => 'Kann ich gefilterte Daten exportieren?',
            'a' => 'Ja. Setzen Sie Ihre Filter (Status, Typ) in der Listenansicht, dann klicken Sie auf Excel oder CSV. Der Export berücksichtigt die aktiven Filter.',
        ],
        [
            'q' => 'Wie kann ich das Onboarding-Tutorial neu starten?',
            'a' => 'Gehen Sie zur Seite Dokumentation und klicken Sie oben rechts auf "Tutorial neu starten".',
        ],
        [
            'q' => 'Was ist der Unterschied zwischen Benutzer- und Admin-Rolle?',
            'a' => 'Benutzer sehen nur ihre eigenen Projekte. Admins sehen alle Projekte aller Benutzer, erhalten wöchentliche Portfolioberichte und können jedes Projekt bearbeiten.',
        ],
        [
            'q' => 'Wie richte ich Slack-Benachrichtigungen ein?',
            'a' => 'Erstellen Sie einen Incoming Webhook in Ihrem Slack-Workspace und fügen Sie die URL unter Profil → Slack/Teams Webhook URL ein. Sowohl tägliche Warnungen als auch wöchentliche Berichte werden dorthin gesendet.',
        ],
    ],
];
