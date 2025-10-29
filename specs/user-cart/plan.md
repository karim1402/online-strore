
# Implementation Plan: User Shopping Cart

**Branch**: `user-cart` | **Date**: 2024-10-29 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/user-cart/spec.md`

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
Implement a shopping cart system for users with single-store constraint. Users can add products with options and addons to their cart. When adding a product from a different store, the existing cart is cleared and replaced. Cart persists across sessions and includes price calculation, stock validation, and full localization support.

## Technical Context
**Language/Version**: PHP 8.1+ / Laravel 10.x  
**Primary Dependencies**: Laravel Framework, Tymon JWT Auth, Spatie Activity Log, LocalizationService  
**Storage**: MySQL 8.0+ with InnoDB engine  
**Testing**: PHPUnit, Laravel Feature Tests, API Tests  
**Target Platform**: Web API (RESTful), supports mobile and web clients
**Project Type**: Web (backend API only)  
**Performance Goals**: <500ms for cart operations, <3 DB queries per cart retrieval  
**Constraints**: Single store per cart (enforced), atomic cart operations, bilingual support (EN/AR)  
**Scale/Scope**: 10k+ concurrent users, 50 items max per cart, real-time price calculation

**User Input Context**: User can only have one cart from one store. Adding product from another store deletes the old cart and creates new one.

## Constitution Check
*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

**Note**: Constitution file is template-only. Applying standard Laravel best practices:

✅ **Single Responsibility**: Each model handles its domain (Cart, CartItem, etc.)  
✅ **RESTful API Design**: Standard REST endpoints with proper HTTP methods  
✅ **Test-Driven**: Feature tests before implementation  
✅ **Database Integrity**: Foreign keys, cascades, constraints enforced  
✅ **Localization**: All user-facing text supports EN/AR  
✅ **Activity Logging**: Cart operations logged via Spatie  

**No Violations**: Standard CRUD with business logic in models/services

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
│   ├── Cart.php
│   ├── CartItem.php
│   ├── CartItemOption.php
│   └── CartItemAddon.php
├── Http/
│   └── Controllers/
│       └── Api/
│           └── User/
│               └── CartController.php
└── Services/
    └── CartService.php (optional, for complex logic)

database/
├── migrations/
│   ├── xxxx_create_carts_table.php
│   ├── xxxx_create_cart_items_table.php
│   ├── xxxx_create_cart_item_options_table.php
│   └── xxxx_create_cart_item_addons_table.php
└── factories/ (for testing)

tests/
└── Feature/
    └── Api/
        └── User/
            └── CartTest.php

routes/
└── api/
    └── user.php (cart routes added here)
```

**Structure Decision**: Laravel API backend structure. Cart feature follows existing pattern:
- Models in `app/Models/` with relationships and business logic
- Controller in `app/Http/Controllers/Api/User/` for authenticated user endpoints
- Migrations in `database/migrations/` for schema
- Feature tests in `tests/Feature/Api/User/` for API validation
- Routes in `routes/api/user.php` with JWT auth middleware
- Activity logging via Spatie trait in models
- Localization via existing LocalizationService

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
- Load `.specify/templates/tasks-template.md` as base
- Generate tasks from Phase 1 design docs (contracts, data model, quickstart)
- Database migrations first (4 tables in dependency order)
- Models with relationships and business logic (4 models)
- Feature tests covering all API endpoints (8 scenarios + 6 validations)
- Controller with all CRUD operations
- Route definitions
- Activity logging integration
- Documentation updates

**Ordering Strategy**:
1. **Database Layer** [P]:
   - Migration: carts table
   - Migration: cart_items table
   - Migration: cart_item_options table
   - Migration: cart_item_addons table

2. **Model Layer** [P]:
   - Cart model with relationships
   - CartItem model with price calculation
   - CartItemOption model
   - CartItemAddon model

3. **Test Layer** (TDD - write tests first):
   - Feature test: Add item to empty cart
   - Feature test: Add item from same store
   - Feature test: Store conflict detection
   - Feature test: Cart replacement
   - Feature test: Update quantity
   - Feature test: Remove item
   - Feature test: View cart
   - Feature test: Clear cart
   - Feature test: Validation scenarios

4. **Controller Layer**:
   - CartController with all endpoints
   - Request validation
   - Store conflict logic
   - Price calculation integration

5. **Integration**:
   - Add routes to user.php
   - Add activity logging
   - Test localization
   - Performance optimization

6. **Documentation**:
   - API documentation
   - Update README
   - Add to CHANGELOG

**Estimated Output**: 30-35 numbered, ordered tasks in tasks.md

**Parallel Execution Opportunities**:
- All migrations can run in sequence but be created in parallel
- All models can be created in parallel
- Test files can be created in parallel
- Documentation can be written in parallel with implementation

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
- [x] Phase 0: Research complete (/plan command)
- [x] Phase 1: Design complete (/plan command)
- [x] Phase 2: Task planning complete (/plan command - describe approach only)
- [ ] Phase 3: Tasks generated (/tasks command)
- [ ] Phase 4: Implementation complete
- [ ] Phase 5: Validation passed

**Gate Status**:
- [x] Initial Constitution Check: PASS
- [x] Post-Design Constitution Check: PASS
- [x] All NEEDS CLARIFICATION resolved
- [x] Complexity deviations documented (none)

**Artifacts Generated**:
- [x] spec.md - Feature specification with clarifications
- [x] research.md - Technical decisions and rationale
- [x] data-model.md - Complete entity design
- [x] contracts/cart-api.yaml - OpenAPI specification
- [x] quickstart.md - Validation scenarios

---
*Based on Constitution v2.1.1 - See `/memory/constitution.md`*
