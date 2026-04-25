# KuantaGrana System Map

## Product view

KuantaGrana is a Laravel application for personal finance management with a gamified journey.
The product is organized around environments, not around isolated modules.

Main user hubs:

- `mapa`: primary entry point and contextual navigation layer
- `dashboard`: consolidated overview of progress, balance, goals, achievements and challenges
- environment pages: the place where the user decides what action makes sense in that context

Global modules still exist, but they now behave mainly as shortcuts into environment-centered flows.

## Core product logic

The system is built around this idea:

1. The user enters the map.
2. The user chooses the right environment.
3. The system exposes only the actions that make sense there.
4. Actions feed the gamification layer and update the dashboard.

This means:

- transactions belong to operational environments
- goals belong to the park environment
- tips belong to educational and contextual experiences
- the dashboard summarizes progress, but it is no longer the main decision surface

## Application layers

### Routing and entry points

- `routes/web.php`
- `DashboardController`: loads the progress overview
- `EnvironmentController`: serves the environment map and each environment page
- `FinancialTransactionController`: environment-guided transaction flow
- `GoalController`: park-centered goal flow

### Business services

- `DashboardService`: aggregates KPIs and dashboard cards
- `EnvironmentExperienceService`: builds map cards and environment pages
- `FinancialTransactionService`: listing, summaries and transaction lifecycle
- `GoalService`: goal lifecycle and contributions
- `GamificationService`: points, levels, achievements and challenges

### Domain models

- `User`: central actor with relations to transactions, goals, achievements, challenges and level
- `Environment`: contextual anchor for the experience
- `FinancialTransaction`: money movement linked to user, category and environment
- `Goal`: objective linked to user and environment
- `GoalContribution`: contribution records for a goal
- `Category`: transaction classification
- `Level`: progression tier
- `Achievement` and `UserAchievement`: one-time unlockables
- `Challenge` and `UserChallenge`: progress-based goals
- `Tip`: educational content per environment

## Environment matrix

Environment capabilities are centralized in `app/Support/EnvironmentCatalog.php`.

| Environment | Purpose | Transactions | Goals | Experience note |
| --- | --- | --- | --- | --- |
| `casa` | base routine and core finances | yes | no | only place that accepts income transactions |
| `escola` | education and orientation | no | no | content and learning environment |
| `mercado` | shopping and food routine | yes | no | operational environment |
| `farmacia` | health and hygiene routine | yes | no | operational environment |
| `parque-de-diversoes` | goals, rewards and progression | no | yes | native home for goals |

## Navigation model

### Primary navigation

- `Mapa`
- `Painel`

These are the main entry points the UI now emphasizes.

### Secondary navigation

- `Transacoes`
- `Metas`

These exist as shortcuts, but no longer represent fully independent product centers.

## Main flows

### 1. Map-driven navigation

1. User logs in.
2. User opens `mapa`.
3. `EnvironmentExperienceService` loads active environments, summaries, highlights and themes.
4. User chooses an environment.
5. The environment page exposes supported actions and recent contextual data.

### 2. Transaction flow

1. User chooses `Casa`, `Mercado` or `Farmacia`.
2. User opens the transaction list or create form from that environment.
3. The selected environment is preserved through the flow.
4. Validation enforces environment capability, category consistency and income restriction to `Casa`.
5. After create, edit or delete, the user returns to the transaction list for that same environment.

### 3. Goal flow

1. User enters `Parque de Diversoes`.
2. The park becomes the native context for listing, creating and editing goals.
3. Contributions are added from the goal management page.
4. When the current amount reaches the target amount, the goal is completed automatically.
5. All goal flow returns stay anchored to the park context.

### 4. Dashboard flow

1. The dashboard aggregates progress from every environment.
2. It highlights level, points, monthly balance, goals, achievements and challenges.
3. Its CTAs encourage the user to go into the map or directly into a meaningful environment such as `Casa` or `Parque`.

## Data relationships

Core relationship chain:

- `User -> hasMany -> FinancialTransaction`
- `User -> hasMany -> Goal`
- `Goal -> hasMany -> GoalContribution`
- `User -> belongsTo -> Level`
- `User -> hasMany -> UserAchievement -> belongsTo -> Achievement`
- `User -> hasMany -> UserChallenge -> belongsTo -> Challenge`
- `Environment -> hasMany -> Category, FinancialTransaction, Goal, Achievement, Challenge, Tip`

## Architectural note

The system previously encoded environment behavior directly in multiple controllers and views through repeated slug checks.
This evolved into a more coherent structure:

- `EnvironmentCatalog`: single source of truth for capabilities, highlights and themes
- `Environment` helpers:
  - `scopeActive()`
  - `scopeSupporting()`
  - `supportsFeature()`
  - `getHighlights()`
  - `getTheme()`

This keeps product rules centralized and makes future environment changes safer.

## Current maturity snapshot

At the current stage, the system is coherent in three important ways:

- conceptual coherence: each domain action has a clear home
- navigation coherence: the map is the dominant entry point
- technical coherence: environment capability rules are centralized and validated consistently
