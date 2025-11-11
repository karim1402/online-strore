
# Implementation Plan: User Checkout

**Branch**: `user-check-out` | **Date**: 2025-10-30 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `C:\Users\Karim Moahmed\Desktop\makkok last\makkok - Copy\specs\user-check-out\spec.md`

## Execution Flow (/plan command scope)
```
1. Load feature spec from Input path
   → If not found: ERROR "No feature spec at {path}"
2. Fill Technical Context (scan for NEEDS CLARIFICATION)
   → Detect Project Type from file system structure or context (web=frontend+backend, mobile=app+api)
   → Set Structure Decision based on project type
3. Fill the Constitution Check section based on the content of the constitution document.
4. Evaluate Constitution Check section below
   → If violations exist: Document in Complexity Tracking
   → If no justification possible: ERROR "Simplify approach first"
   → Update Progress Tracking: Initial Constitution Check
5. Execute Phase 0 → research.md
   → If NEEDS CLARIFICATION remain: ERROR "Resolve unknowns"
6. Execute Phase 1 → contracts, data-model.md, quickstart.md, agent-specific template file (e.g., `CLAUDE.md` for Claude Code, `.github/copilot-instructions.md` for GitHub Copilot, `GEMINI.md` for Gemini CLI, `QWEN.md` for Qwen Code or `AGENTS.md` for opencode).
7. Re-evaluate Constitution Check section
   → If new violations: Refactor design, return to Phase 1
   → Update Progress Tracking: Post-Design Constitution Check
8. Plan Phase 2 → Describe task generation approach (DO NOT create tasks.md)
9. STOP - Ready for /tasks command
```

**IMPORTANT**: The /plan command STOPS at step 7. Phases 2-4 are executed by other commands:
- Phase 2: /tasks command creates tasks.md
- Phase 3-4: Implementation execution (manual or via tools)

## Summary
Enable authenticated users to checkout their cart and create orders with two payment methods: cash on delivery and online payment (placeholder). The system validates cart items, creates orders with item snapshots, handles payment method selection, and manages order lifecycle. Online payment gateway integration is intentionally left as placeholder for future implementation.

## Technical Context
**Language/Version**: PHP 8.1+ / Laravel 10.x  
**Primary Dependencies**: Tymon JWT Auth, Spatie Activity Log, LocalizationService  
**Storage**: MySQL database with InnoDB engine  
**Testing**: PHPUnit, Laravel Feature Tests  
**Target Platform**: Linux server / Web API
**Project Type**: Backend API (Laravel)  
**Performance Goals**: Checkout completion < 2 seconds, cart validation < 1 second  
**Constraints**: Transaction-based order creation, atomic cart-to-order conversion, max 5 queries for validation  
**Scale/Scope**: Support 50 items per cart, concurrent checkouts, bilingual support (EN/AR)

**User Context**: Checkout feature with cash and online payment. Online payment gateway integration is placeholder - no actual payment processing logic in v1.

## Constitution Check
*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

**Status**: ✅ PASS (Constitution file is template - no specific constraints defined)

**Notes**: 
- Following existing Laravel project patterns
- Using established dependencies (JWT, Activity Log, Localization)
- RESTful API design consistent with existing endpoints
- No new architectural complexity introduced

## Project Structure

### Documentation (this feature)
```
specs/[###-feature]/
├── plan.md              # This file (/plan command output)
├── research.md          # Phase 0 output (/plan command)
├── data-model.md        # Phase 1 output (/plan command)
├── quickstart.md        # Phase 1 output (/plan command)
├── contracts/           # Phase 1 output (/plan command)
└── tasks.md             # Phase 2 output (/tasks command - NOT created by /plan)
```

### Source Code (repository root)
<!--
  ACTION REQUIRED: Replace the placeholder tree below with the concrete layout
  for this feature. Delete unused options and expand the chosen structure with
  real paths (e.g., apps/admin, packages/something). The delivered plan must
  not include Option labels.
-->
```
# Laravel API Structure (Backend Only)
app/
├── Models/
│   ├── Order.php
│   ├── OrderItem.php
│   ├── OrderItemOption.php
│   └── OrderItemAddon.php
├── Http/
│   └── Controllers/
│       └── Api/
│           └── User/
│               └── OrderController.php
└── Services/
    └── (optional) OrderService.php

database/
├── migrations/
│   ├── xxxx_create_orders_table.php
│   ├── xxxx_create_order_items_table.php
│   ├── xxxx_create_order_item_options_table.php
│   └── xxxx_create_order_item_addons_table.php

routes/
└── api/
    └── user.php (add order routes)

resources/
└── lang/
    ├── en/messages.json (add order messages)
    └── ar/messages.json (add order messages)

tests/
└── Feature/
    └── OrderTest.php
```

**Structure Decision**: Laravel API backend structure. All code in existing Laravel application at `C:\Users\Karim Moahmed\Desktop\makkok last\makkok - Copy\`. Feature adds 4 models, 1 controller, 4 migrations, routes, and localization messages. Follows existing cart feature patterns.

## Phase 0: Outline & Research
1. **Extract unknowns from Technical Context** above:
   - For each NEEDS CLARIFICATION → research task
   - For each dependency → best practices task
   - For each integration → patterns task

2. **Generate and dispatch research agents**:
   ```
   For each unknown in Technical Context:
     Task: "Research {unknown} for {feature context}"
   For each technology choice:
     Task: "Find best practices for {tech} in {domain}"
   ```

3. **Consolidate findings** in `research.md` using format:
   - Decision: [what was chosen]
   - Rationale: [why chosen]
   - Alternatives considered: [what else evaluated]

**Output**: research.md with all NEEDS CLARIFICATION resolved

## Phase 1: Design & Contracts
*Prerequisites: research.md complete*

1. **Extract entities from feature spec** → `data-model.md`:
   - Entity name, fields, relationships
   - Validation rules from requirements
   - State transitions if applicable

2. **Generate API contracts** from functional requirements:
   - For each user action → endpoint
   - Use standard REST/GraphQL patterns
   - Output OpenAPI/GraphQL schema to `/contracts/`

3. **Generate contract tests** from contracts:
   - One test file per endpoint
   - Assert request/response schemas
   - Tests must fail (no implementation yet)

4. **Extract test scenarios** from user stories:
   - Each story → integration test scenario
   - Quickstart test = story validation steps

5. **Update agent file incrementally** (O(1) operation):
   - Run `.specify/scripts/powershell/update-agent-context.ps1 -AgentType windsurf`
     **IMPORTANT**: Execute it exactly as specified above. Do not add or remove any arguments.
   - If exists: Add only NEW tech from current plan
   - Preserve manual additions between markers
   - Update recent changes (keep last 3)
   - Keep under 150 lines for token efficiency
   - Output to repository root

**Output**: data-model.md, /contracts/*, failing tests, quickstart.md, agent-specific file

## Phase 2: Task Planning Approach
*This section describes what the /tasks command will do - DO NOT execute during /plan*

**Task Generation Strategy**:
1. **Database Layer** (4 migrations):
   - Create orders table migration
   - Create order_items table migration
   - Create order_item_options table migration
   - Create order_item_addons table migration
   - Run migrations

2. **Model Layer** (4 models + relationships):
   - Create Order model with relationships and activity logging
   - Create OrderItem model with price calculations
   - Create OrderItemOption model
   - Create OrderItemAddon model
   - Update User model (add orders relationship)

3. **Controller Layer**:
   - Create OrderController with 4 endpoints:
     * POST /checkout (main checkout logic)
     * GET /orders (list with pagination)
     * GET /orders/{id} (single order)
     * POST /orders/{id}/cancel (cancellation)

4. **Routes & Localization**:
   - Add routes to routes/api/user.php
   - Add order messages to resources/lang/en/messages.json
   - Add order messages to resources/lang/ar/messages.json

5. **Testing** (optional for v1):
   - Feature tests for all scenarios
   - Validation tests
   - Performance tests

**Ordering Strategy**:
- Database first (migrations)
- Models second (with relationships)
- Controller third (business logic)
- Routes and localization fourth
- Tests last (validation)

**Estimated Output**: 30-35 numbered, ordered tasks in tasks.md

**IMPORTANT**: This phase is executed by the /tasks command, NOT by /plan

## Phase 3+: Future Implementation
*These phases are beyond the scope of the /plan command*

**Phase 3**: Task execution (/tasks command creates tasks.md)  
**Phase 4**: Implementation (execute tasks.md following constitutional principles)  
**Phase 5**: Validation (run tests, execute quickstart.md, performance validation)

## Complexity Tracking
*Fill ONLY if Constitution Check has violations that must be justified*

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| [e.g., 4th project] | [current need] | [why 3 projects insufficient] |
| [e.g., Repository pattern] | [specific problem] | [why direct DB access insufficient] |


## Progress Tracking
*This checklist is updated during execution flow*

**Phase Status**:
- [x] Phase 0: Research complete (/plan command) ✅
- [x] Phase 1: Design complete (/plan command) ✅
- [x] Phase 2: Task planning complete (/plan command - describe approach only) ✅
- [ ] Phase 3: Tasks generated (/tasks command)
- [ ] Phase 4: Implementation complete
- [ ] Phase 5: Validation passed

**Gate Status**:
- [x] Initial Constitution Check: PASS ✅
- [x] Post-Design Constitution Check: PASS ✅
- [x] All NEEDS CLARIFICATION resolved ✅
- [x] Complexity deviations documented: N/A ✅

**Artifacts Generated**:
- [x] research.md - 10 research questions resolved
- [x] data-model.md - 4 tables with complete schemas
- [x] contracts/order-api.yaml - OpenAPI 3.0 specification
- [x] quickstart.md - Comprehensive validation guide
- [ ] tasks.md - Awaiting /tasks command

---
*Based on Constitution v2.1.1 - See `/memory/constitution.md`*
