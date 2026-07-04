# Graph Report - C:/OSPanel/home/account-ok  (2026-06-24)

## Corpus Check
- Large corpus: 805 files � ~2,931,718 words. Semantic extraction will be expensive (many Claude tokens). Consider running on a subfolder.

## Summary
- 634 nodes · 1052 edges · 106 communities (92 shown, 14 thin omitted)
- Extraction: 96% EXTRACTED · 4% INFERRED · 0% AMBIGUOUS · INFERRED: 40 edges (avg confidence: 0.8)
- Token cost: 0 input · 0 output

## Community Hubs (Navigation)
- [[_COMMUNITY_Eloquent Base Models|Eloquent Base Models]]
- [[_COMMUNITY_Cabinet Controllers|Cabinet Controllers]]
- [[_COMMUNITY_City & Axios Controllers|City & Axios Controllers]]
- [[_COMMUNITY_MoonShine Index Pages|MoonShine Index Pages]]
- [[_COMMUNITY_Frontend JavaScript|Frontend JavaScript]]
- [[_COMMUNITY_Home Controller & Forms|Home Controller & Forms]]
- [[_COMMUNITY_User Auth Traits|User Auth Traits]]
- [[_COMMUNITY_Auth Controller|Auth Controller]]
- [[_COMMUNITY_Contractor Form Pages|Contractor Form Pages]]
- [[_COMMUNITY_Form Validation Rules|Form Validation Rules]]
- [[_COMMUNITY_App Layout & Theme|App Layout & Theme]]
- [[_COMMUNITY_Page Templates & Options|Page Templates & Options]]
- [[_COMMUNITY_Document Form Page|Document Form Page]]
- [[_COMMUNITY_Invoice Resource Admin|Invoice Resource Admin]]
- [[_COMMUNITY_City Form Page|City Form Page]]
- [[_COMMUNITY_Document Resource Admin|Document Resource Admin]]
- [[_COMMUNITY_Slide Animations|Slide Animations]]
- [[_COMMUNITY_App Service Provider|App Service Provider]]
- [[_COMMUNITY_News Form Page|News Form Page]]
- [[_COMMUNITY_Act Item Resource|Act Item Resource]]
- [[_COMMUNITY_Act Resource Admin|Act Resource Admin]]
- [[_COMMUNITY_Contractor Resource Admin|Contractor Resource Admin]]
- [[_COMMUNITY_Individual Entrepreneur Resource|Individual Entrepreneur Resource]]
- [[_COMMUNITY_Invoice Item Resource|Invoice Item Resource]]
- [[_COMMUNITY_Legal Entity Resource|Legal Entity Resource]]
- [[_COMMUNITY_MoonShine User Resource|MoonShine User Resource]]
- [[_COMMUNITY_mz-select Component|mz-select Component]]
- [[_COMMUNITY_Self Employed Resource|Self Employed Resource]]
- [[_COMMUNITY_User Resource Admin|User Resource Admin]]
- [[_COMMUNITY_City Resource Admin|City Resource Admin]]
- [[_COMMUNITY_Datepicker UI|Datepicker UI]]
- [[_COMMUNITY_Invoice Form Page|Invoice Form Page]]
- [[_COMMUNITY_Blade Layout Templates|Blade Layout Templates]]

## God Nodes (most connected - your core abstractions)
1. `Controller` - 26 edges
2. `Contractor` - 19 edges
3. `ResourcesController` - 15 edges
4. `Invoice` - 15 edges
5. `User` - 12 edges
6. `Setting` - 11 edges
7. `SettingsController` - 10 edges
8. `AboutController` - 10 edges
9. `CityFormPage` - 10 edges
10. `DocumentFormPage` - 10 edges

## Surprising Connections (you probably didn't know these)
- `AuthController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Auth/AuthController.php → app/Http/Controllers/Controller.php
- `CabinetController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Cabinet/CabinetController.php → app/Http/Controllers/Controller.php
- `HomeController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/HomeController.php → app/Http/Controllers/Controller.php
- `PageController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Pages/PageController.php → app/Http/Controllers/Controller.php
- `CityController` --inherits--> `Controller`  [EXTRACTED]
  app/Http/Controllers/Ajax/CityController.php → app/Http/Controllers/Controller.php

## Import Cycles
- None detected.

## Hyperedges (group relationships)
- **mz-select CSS, JS, and HTML form the complete mz-select component** — select_readme_mz_select_css, select_readme_mz_select_js, select_readme_mz_select_component [EXTRACTED 1.00]

## Communities (106 total, 14 thin omitted)

### Community 0 - "Eloquent Base Models"
Cohesion: 0.05
Nodes (17): BelongsTo, Builder, Model, Act, ActItem, City, Contract, Document (+9 more)

### Community 1 - "Cabinet Controllers"
Cohesion: 0.06
Nodes (14): CabinetController, Closure, Component, CitySelector, Download, LengthAwarePaginator, TopMenu, AboutController (+6 more)

### Community 2 - "City & Axios Controllers"
Cohesion: 0.10
Nodes (16): CityController, AxiosController, ContractorsController, ContractsController, InvoicesController, SettingsController, SmartInvoicesController, Carbon (+8 more)

### Community 3 - "MoonShine Index Pages"
Cohesion: 0.06
Nodes (15): ComponentContract, IndexPage, ActIndexPage, CityIndexPage, ContractorIndexPage, DocumentIndexPage, IndividualEntrepreneurIndexPage, InvoiceIndexPage (+7 more)

### Community 4 - "Frontend JavaScript"
Cohesion: 0.09
Nodes (23): axiosLaravel(), cabinetMessageDeleteInit(), scrollCabinetMessages(), fancyWindows, metaElements, openFancyBox(), flash_message(), asyncExecution() (+15 more)

### Community 5 - "Home Controller & Forms"
Cohesion: 0.13
Nodes (7): BelongsToMany, HomeController, FormBuilder, Setting, Page, Dashboard, HomePage

### Community 6 - "User Auth Traits"
Cohesion: 0.14
Nodes (6): Authenticatable, HasFactory, HasMany, HasOne, User, Notifiable

### Community 7 - "Auth Controller"
Cohesion: 0.16
Nodes (5): AuthController, LoginRequest, RegisterRequest, FormRequest, RedirectResponse

### Community 8 - "Contractor Form Pages"
Cohesion: 0.15
Nodes (5): FormPage, ContractorFormPage, IndividualEntrepreneurFormPage, LegalEntityFormPage, SelfEmployedFormPage

### Community 9 - "Form Validation Rules"
Cohesion: 0.16
Nodes (4): DataWrapperContract, MoonShineUserFormPage, MoonShineUserRoleFormPage, UserFormPage

### Community 10 - "App Layout & Theme"
Cohesion: 0.20
Nodes (4): AppLayout, ColorManagerContract, AxeldLayout, MoonShineLayout

### Community 11 - "Page Templates & Options"
Cohesion: 0.18
Nodes (7): label(), toOptions(), label(), toOptions(), label(), toOptions(), self

### Community 12 - "Document Form Page"
Cohesion: 0.16
Nodes (3): DocumentFormPage, FileNaming, UploadedFile

### Community 13 - "Invoice Resource Admin"
Cohesion: 0.18
Nodes (3): InvoiceResource, ListOf, MoonShineUserRoleResource

### Community 15 - "Document Resource Admin"
Cohesion: 0.22
Nodes (3): DocumentResource, ModelResource, NewsResource

### Community 16 - "Slide Animations"
Cohesion: 0.38
Nodes (3): slideDown(), slideToggle(), slideUp()

### Community 17 - "App Service Provider"
Cohesion: 0.31
Nodes (4): CoreContract, AppServiceProvider, MoonShineServiceProvider, ServiceProvider

### Community 26 - "mz-select Component"
Cohesion: 0.47
Nodes (6): index.html (demo page), mz-select Component, mz-select.css, mz-select.js, Standalone Without Materialize, Standalone Styled Select

### Community 30 - "Datepicker UI"
Cohesion: 0.83
Nodes (3): datepicker_accountant_ticket_date(), datepicker_date_birthday(), topPositionLabel()

## Knowledge Gaps
- **6 isolated node(s):** `metaElements`, `fancyWindows`, `metaElements`, `templates.axeld.header`, `templates.axeld.footer` (+1 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **14 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Contractor` connect `City & Axios Controllers` to `Eloquent Base Models`, `MoonShine Index Pages`, `User Auth Traits`, `Contractor Resource Admin`, `Invoice Form Page`?**
  _High betweenness centrality (0.143) - this node is a cross-community bridge._
- **Why does `Invoice` connect `Eloquent Base Models` to `City & Axios Controllers`, `Invoice Resource Admin`, `User Auth Traits`?**
  _High betweenness centrality (0.088) - this node is a cross-community bridge._
- **Why does `Setting` connect `Home Controller & Forms` to `Eloquent Base Models`, `Cabinet Controllers`, `Page Templates & Options`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._
- **What connects `metaElements`, `fancyWindows`, `metaElements` to the rest of the system?**
  _6 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Eloquent Base Models` be split into smaller, more focused modules?**
  _Cohesion score 0.051560379918588875 - nodes in this community are weakly interconnected._
- **Should `Cabinet Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.060655737704918035 - nodes in this community are weakly interconnected._
- **Should `City & Axios Controllers` be split into smaller, more focused modules?**
  _Cohesion score 0.1038961038961039 - nodes in this community are weakly interconnected._