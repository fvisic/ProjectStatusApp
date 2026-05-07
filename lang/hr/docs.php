<?php

return [
    'nav' => 'Docs',
    'title' => 'Dokumentacija',
    'restart_tutorial' => 'Ponovo pokreni vodič',

    // Sidebar
    'nav_overview' => 'Pregled',
    'nav_projects' => 'Projekti',
    'nav_views' => 'Prikazi',
    'nav_dashboard' => 'Dashboard',
    'nav_exports' => 'Exporti i PDF',
    'nav_notifications' => 'Obavijesti',
    'nav_security' => 'Sigurnost',
    'nav_settings' => 'Postavke',
    'nav_faq' => 'FAQ',

    // Security
    'security_title' => 'Sigurnost',
    'security_intro' => 'ProjectStatus nudi više slojeva sigurnosti računa: kontrolu pristupa po ulogama, dvofaktorsku autentikaciju (TOTP), passkey ključeve (WebAuthn) i administratorske alate za upravljanje korisnicima.',
    'security_roles_title' => 'Uloge',
    'security_roles_intro' => 'Svaki korisnik ima točno jednu ulogu. Uloge određuju što korisnik može vidjeti i raditi u aplikaciji.',
    'security_role_admin_desc' => 'Pun pristup. Može vidjeti, uređivati i brisati svaki projekt. Može upravljati korisnicima, mijenjati uloge, resetirati lozinke, isključiti 2FA/passkey i preuzeti identitet drugog korisnika.',
    'security_role_manager_desc' => 'Pristup za čitanje svih projekata i izvještaja. Može uređivati samo svoje projekte.',
    'security_role_user_desc' => 'Vidi i uređuje samo projekte koje posjeduje.',

    'security_2fa_title' => 'Dvofaktorska autentikacija (TOTP)',
    'security_2fa_intro' => 'Dodajte jednokratni kod iz autentikator aplikacije pri svakoj prijavi. Preporučeno za sve korisnike s osjetljivim pristupom.',
    'security_2fa_s1' => 'Otvorite Profil → Dvofaktorska autentikacija i kliknite Uključi.',
    'security_2fa_s2' => 'Skenirajte QR kod aplikacijom Google Authenticator, 1Password, Authy ili bilo kojom TOTP aplikacijom.',
    'security_2fa_s3' => 'Unesite 6-znamenkasti kod koji pokazuje aplikacija za potvrdu.',
    'security_2fa_s4' => 'Spremite prikazane kodove za oporavak - svaki se može iskoristiti jednom ako izgubite autentikator.',
    'security_2fa_recovery_tip' => 'Kodovi za oporavak su jednokratni. Spremite ih u upravitelj lozinki. Nakon korištenja jednog generirajte nove s istog ekrana.',

    'security_passkeys_title' => 'Passkey ključevi (WebAuthn)',
    'security_passkeys_intro' => 'Prijavite se s Touch ID-om, Face ID-om, Windows Hello-om ili hardverskim sigurnosnim ključem (YubiKey). Bez tipkanja lozinke. Možete registrirati više uređaja.',
    'security_passkeys_s1' => 'Otvorite Profil → Passkey.',
    'security_passkeys_s2' => 'Upišite naziv uređaja (npr. "MacBook Touch ID") i kliknite Dodaj passkey.',
    'security_passkeys_s3' => 'Potvrdite otiskom prsta, licem, PIN-om ili hardverskim ključem kad preglednik zatraži.',
    'security_passkeys_s4' => 'Na ekranu za prijavu kliknite Prijavi se passkey-em umjesto upisivanja lozinke.',
    'security_passkeys_hybrid_tip' => 'Passkey ključevi mogu istovremeno postojati uz TOTP i lozinku. Možete koristiti što je najprikladnije na svakom uređaju.',

    'security_users_title' => 'Administratorsko upravljanje korisnicima',
    'security_users_intro' => 'Administratori mogu upravljati svim korisnicima sa stranice Korisnici (samo admin). Akcije po korisniku:',
    'security_users_action_reset_password' => 'Resetiraj lozinku - generira novu privremenu lozinku za podijeliti s korisnikom.',
    'security_users_action_disable' => 'Onemogući račun - blokira prijavu bez brisanja podataka.',
    'security_users_action_disable_2fa' => 'Isključi 2FA - briše TOTP secret korisnika ako je izgubio autentikator.',
    'security_users_action_reset_passkeys' => 'Resetiraj passkey - briše sve registrirane passkey ključeve kad korisnik izgubi sve uređaje.',
    'security_users_action_change_role' => 'Promijeni ulogu - promakni ili degradiraj između admin / voditelj / korisnik.',
    'security_users_action_delete' => 'Obriši - trajno uklanjanje (ne može se poništiti).',

    'security_impersonation_title' => 'Preuzimanje identiteta',
    'security_impersonation_desc' => 'Administratori se mogu prijaviti kao bilo koji ne-admin korisnik sa stranice Korisnici. Žuti baner se pojavljuje na vrhu dok je preuzimanje aktivno; kliknite "Vrati se" za povratak na vlastitu sesiju. Nije moguće preuzeti drugog admina ni ulančavati preuzimanja.',

    // Overview
    'overview_title' => 'Project Status - Pregled',
    'overview_intro' => 'Project Status je alat za upravljanje portfeljom projekata dizajniran za voditelje timova i projektne menadžere. Pomaže vam pratiti zdravlje projekata, rokove, budgete, rizike i isporuke.',
    'feature_tracking' => 'Praćenje projekata',
    'feature_dashboard' => 'Portfolio Dashboard',
    'feature_reports' => 'Izvještaji i export',
    'feature_notifications' => 'Obavijesti',
    'feature_history' => 'Povijest verzija',
    'feature_i18n' => 'EN / HR jezici',
    'overview_roles_title' => 'Korisničke uloge',
    'role' => 'Uloga',
    'role_permissions' => 'Dozvole',
    'role_user' => 'Korisnik',
    'role_user_desc' => 'Kreiranje, uređivanje i brisanje vlastitih projekata. Pregled vlastitih dashboard metrika.',
    'role_admin' => 'Admin',
    'role_admin_desc' => 'Sve dozvole korisnika plus: pregled svih projekata svih korisnika, primanje tjednih portfolio izvještaja, uređivanje bilo kojeg projekta.',

    // Projects
    'projects_title' => 'Rad s projektima',
    'projects_intro' => 'Svaki projekt prati kompletan životni ciklus implementacije, od početne analize do go-livea i hypercare-a.',
    'projects_create_title' => 'Kreiranje novog projekta',
    'projects_create_s1' => 'Navigirajte na Projekti → Lista i kliknite "+ Novi projekt"',
    'projects_create_s2' => 'Popunite tab Osnovno: naziv, klijent, TL, tip projekta, datumi i health status',
    'projects_create_s3' => 'Konfigurirajte Faze: postavite status i datum završetka za svaku od 7 faza',
    'projects_create_s4' => 'Spremite projekt. Snimka se automatski kreira za povijest verzija.',
    'projects_tabs_title' => 'Tabovi forme projekta',
    'tab_basic_desc' => 'Metapodaci projekta - naziv, klijent, TL, tip (New/Migration/CR), datumi, health status i sljedeći koraci.',
    'tab_phases_desc' => '7 implementacijskih faza s praćenjem statusa, ključnim aktivnostima, oznakama potvrde klijenta i napomenama.',
    'tab_estimation_desc' => 'Praćenje budgeta - estimirani sati, utrošeni sati, preostala procjena, delta prognoza s automatskim alarmima.',
    'tab_risks_desc' => 'Registar rizika s razinama ozbiljnosti (Low/Medium/High) i planovima mitigacije. Produktne obavijesti s rokovima.',
    'tab_burndown_desc' => 'Vizualni burndown grafikon koji prikazuje estimirane, utrošene i preostale sate kroz vrijeme (potrebne 2+ snimke).',
    'projects_health_title' => 'Health status',
    'health_on_track_desc' => 'Projekt napreduje prema planu. Nema problema ili kašnjenja.',
    'health_at_risk_desc' => 'Identificirani potencijalni problemi. Potreban pojačani nadzor.',
    'health_off_track_desc' => 'Značajna kašnjenja ili blokade. Potrebna eskalacija.',
    'projects_phases_title' => 'Životni ciklus projekta',
    'projects_phases_intro' => 'Svaki projekt prati životni ciklus od 7 faza. Svaka faza se može pratiti sa statusom, datumima i napomenama:',

    // Views
    'views_title' => 'Prikazi projekata',
    'view_list_title' => 'Lista',
    'view_list_full_desc' => 'Zadani prikaz pokazuje sve projekte u tablici sa stupcima za naziv, klijent, tip, fazu, health i go-live datum. Koristite pretragu za pronalaženje projekata po nazivu ili klijentu, i filtrirajte po health statusu ili tipu.',
    'view_list_tip' => 'Kliknite ikonu olovke na bilo kojem retku za brzu izmjenu - promijenite health status ili fazu bez otvaranja pune forme.',
    'view_kanban_title' => 'Kanban ploča',
    'view_kanban_full_desc' => 'Kanban prikaz organizira projekte u tri stupca po health statusu: On Track, At Risk i Off Track. Svaka kartica pokazuje naziv projekta, klijenta, fazu, go-live datum i traku napretka budgeta.',
    'view_kanban_tip' => 'Povucite i ispustite kartice između stupaca za trenutnu promjenu health statusa projekta. Snimka se automatski kreira.',
    'view_timeline_title' => 'Vremenska crta / Gantt',
    'view_timeline_full_desc' => 'Vremenska crta prikazuje projekte kao horizontalne trake od početka projekta do planiranog go-livea. Trake su obojene prema health statusu. Crvena vertikalna linija označava današnji datum.',
    'view_timeline_tip' => 'Koristite kontrole zooma (ikone povećala) za prebacivanje između kvartalne, mjesečne i tjedne granulacije.',
    'tip' => 'Savjet',

    // Dashboard
    'dashboard_title' => 'Dashboard',
    'dashboard_intro' => 'Dashboard pruža pregled vašeg portfelja projekata u realnom vremenu s KPI karticama, grafovima trendova i alarmima.',
    'dashboard_kpi_title' => 'KPI kartice',
    'dashboard_kpi_desc' => 'Gornji red prikazuje: ukupne projekte, distribuciju health statusa (On Track / At Risk / Off Track), distribuciju tipova i distribuciju faza. Admini vide metrike svih korisnika.',
    'dashboard_trends_title' => 'Grafovi trendova',
    'dashboard_trends_desc' => 'Dva grafa za 8 tjedana pokazuju: (1) Health trend - stupičasti graf koji prikazuje kako se omjer on-track/at-risk/off-track mijenja tjedan po tjedan, i (2) Utrošeni sati trend - grafikon koji prati kumulativne utrošene sate.',
    'dashboard_alerts_title' => 'Alarmi',
    'alert_offtrack' => 'Off-track projekti koji zahtijevaju hitnu pažnju',
    'alert_golive' => 'Projekti s go-live datumom unutar sljedećih 30 dana',
    'alert_budget' => 'Projekti koji prekoračuju budget za više od 15%',

    // Exports
    'exports_title' => 'Exporti i izvještaji',
    'exports_intro' => 'Generirajte profesionalne izvještaje u više formata za dionike i evidenciju.',
    'export_single_pdf_title' => 'PDF pojedinačnog projekta',
    'export_single_pdf_desc' => 'Kliknite "PDF" u popisu projekata za generiranje detaljnog A4 izvještaja sa svim metapodacima, fazama, rizicima, detaljima estimacije i sljedećim koracima. Savršeno za steering committee prezentacije.',
    'export_portfolio_title' => 'Portfolio PDF',
    'export_portfolio_desc' => 'Kliknite "Portfolio PDF" na Dashboardu za generiranje pejzažnog sažetka svih projekata s KPI-jevima, pregledom zdravlja i karticama pojedinačnih projekata. Idealno za izvršno izvještavanje.',
    'export_excel_title' => 'Excel / CSV export',
    'export_excel_desc' => 'Exportirajte filtrirani popis projekata kao XLSX ili CSV iz prikaza Liste. Export poštuje vaše trenutne health/tip filtere i uključuje sva ključna polja.',

    // Notifications
    'notifications_title' => 'Obavijesti',
    'notifications_intro' => 'Aplikacija šalje automatizirane alarme kako biste bili informirani o rizicima i prekretnicama projekata.',
    'notif_daily_title' => 'Dnevni alarmi (08:00)',
    'notif_daily_desc' => 'Svako jutro sustav provjerava: off-track projekte, go-live datume unutar 7 dana i prekoračenja budgeta veća od 15%. Vlasnici projekata primaju email obavijesti za svoje zahvaćene projekte.',
    'notif_weekly_title' => 'Tjedni izvještaj (ponedjeljak 07:00)',
    'notif_weekly_desc' => 'Admini primaju sveobuhvatan tjedni sažetak portfelja svakog ponedjeljka ujutro. Uključuje brojeve projekata, razlomak zdravlja, statistike estimacija, popis off-track projekata i nadolazeće go-liveove.',
    'notif_webhook_title' => 'Slack / Teams Webhooks',
    'notif_webhook_desc' => 'Uz email, alarmi se mogu isporučiti u Slack ili Microsoft Teams kanale putem dolaznih webhookova.',
    'notif_webhook_setup' => 'Postavke:',
    'notif_webhook_s1' => 'Kreirajte Incoming Webhook u svom Slack/Teams workspaceu',
    'notif_webhook_s2' => 'Idite na Profil → Slack/Teams Webhook URL i zalijepite URL',
    'notif_webhook_s3' => 'Alarmi će se sada slati i na email i na vaš webhook',

    // Settings
    'settings_title' => 'Postavke',
    'settings_intro' => 'Prilagodite svoje iskustvo sa stranice Profil.',
    'settings_profile_title' => 'Profil',
    'settings_profile_desc' => 'Ažurirajte svoje ime, email i lozinku sa stranice Profil (dostupna putem padajućeg izbornika u gornjem desnom kutu).',
    'settings_language_title' => 'Jezik',
    'settings_language_desc' => 'Prebacujte se između engleskog i hrvatskog koristeći preklopnik jezika u navigacijskoj traci. Vaša preferencija se sprema i perzistira između sesija.',
    'settings_webhook_title' => 'Webhook URL',
    'settings_webhook_desc' => 'Dodajte svoj Slack ili Teams webhook URL na stranici Profil za primanje projektnih alarma u svom timskom kanalu.',

    // FAQ
    'faq_title' => 'Često postavljana pitanja',
    'faqs' => [
        [
            'q' => 'Kako brzo promijeniti health status projekta?',
            'a' => 'Koristite Kanban ploču - povucite karticu iz jednog stupca u drugi. Ili kliknite ikonu olovke u prikazu Liste za brzu izmjenu.',
        ],
        [
            'q' => 'Mogu li vidjeti tko je što promijenio na projektu?',
            'a' => 'Da. Svako spremanje kreira snimku u Povijesti verzija. Kliknite "Povijest" pored projekta za pregled svih verzija i usporedbu promjena između bilo koje dvije.',
        ],
        [
            'q' => 'Što pokreće email alarm?',
            'a' => 'Tri stvari: (1) Projekt označen kao Off Track, (2) Go-live datum unutar 7 dana, (3) Prekoračenje budgeta veće od 15%. Alarmi se šalju dnevno u 08:00.',
        ],
        [
            'q' => 'Kako funkcionira burndown grafikon?',
            'a' => 'Burndown grafikon prikazuje estimirane, utrošene i preostale sate kroz vrijeme. Svako spremanje projekta kreira podatkovnu točku. Potrebna su najmanje 2 spremanja.',
        ],
        [
            'q' => 'Mogu li exportirati filtrirane podatke?',
            'a' => 'Da. Postavite filtere (health, tip) u prikazu Liste, zatim kliknite Excel ili CSV. Export poštuje aktivne filtere.',
        ],
        [
            'q' => 'Kako ponovo pokrenuti vodič za upoznavanje?',
            'a' => 'Idite na stranicu Dokumentacija i kliknite "Ponovo pokreni vodič" u gornjem desnom kutu.',
        ],
        [
            'q' => 'Koja je razlika između korisničke i admin uloge?',
            'a' => 'Korisnici vide samo svoje projekte. Admini vide sve projekte svih korisnika, primaju tjedne portfolio izvještaje i mogu uređivati bilo koji projekt.',
        ],
        [
            'q' => 'Kako postaviti Slack obavijesti?',
            'a' => 'Kreirajte Incoming Webhook u svom Slack workspaceu, zatim zalijepite URL u Profil → Slack/Teams Webhook URL. I dnevni alarmi i tjedni izvještaji će se tamo slati.',
        ],
    ],
];
