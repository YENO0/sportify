# Inventory/Equipment Management Module Documentation

## Overview
This module implements a comprehensive inventory management system for the Sporty sports club management application. It follows the MVC (Model-View-Controller) architecture pattern and incorporates two design patterns: **Factory Method** and **Decorator**.

## Design Patterns Implementation

### 1. Factory Method Pattern

**Location:** `app/Patterns/Factory/`

**Purpose:** The Factory Method pattern is used to create different types of equipment (Sports, Gym, Outdoor) with type-specific default values and behaviors.

**Structure:**
- `EquipmentFactoryInterface.php` - Interface defining the contract for equipment factories
- `AbstractEquipmentFactory.php` - Abstract base class providing template method pattern
- `SportsEquipmentFactory.php` - Concrete factory for sports equipment
- `GymEquipmentFactory.php` - Concrete factory for gym equipment
- `OutdoorEquipmentFactory.php` - Concrete factory for outdoor equipment
- `EquipmentFactoryManager.php` - Manager class for easy factory access

**How it works:**
1. Each equipment type has its own factory class
2. Factories set type-specific defaults (location, maintenance schedules, etc.)
3. The `EquipmentFactoryManager` provides a simple interface to get the appropriate factory
4. Usage: `EquipmentFactoryManager::create('sports', $data)`

**Benefits:**
- Encapsulates object creation logic
- Makes it easy to add new equipment types
- Ensures consistent initialization for each type
- Follows Open/Closed Principle

**Example Usage:**
```php
// In InventoryController::store()
$equipment = EquipmentFactoryManager::create($request->type, $request->all());
```

### 2. Decorator Pattern

**Location:** `app/Patterns/Decorator/`

**Purpose:** The Decorator pattern allows adding features (insurance, warranty, maintenance tracking) to equipment dynamically without modifying the base Equipment class.

**Structure:**
- `EquipmentDecoratorInterface.php` - Interface for all decorators
- `BaseEquipmentDecorator.php` - Base decorator class with common functionality
- `InsuranceDecorator.php` - Adds insurance coverage feature
- `WarrantyDecorator.php` - Adds warranty coverage feature
- `MaintenanceTrackingDecorator.php` - Adds automated maintenance tracking
- `EquipmentDecoratorManager.php` - Manager for applying multiple decorators

**How it works:**
1. Each decorator wraps an Equipment object
2. Decorators add features by creating `EquipmentFeature` records
3. Multiple decorators can be chained together
4. Features are stored in the database and displayed in the UI

**Benefits:**
- Adds features dynamically without modifying base class
- Allows flexible combination of features
- Follows Single Responsibility Principle
- Easy to add new feature types

**Example Usage:**
```php
// In InventoryController::store()
$decoratorManager = new EquipmentDecoratorManager($equipment);
$decoratorManager->withInsurance(100.00, $expiryDate)
                 ->withWarranty('Extended', $warrantyExpiry)
                 ->withMaintenanceTracking(3)
                 ->apply();
```

## MVC Architecture

### Models
- **Equipment** (`app/Models/Equipment.php`)
  - Main equipment model with relationships to features and transactions
  - Includes helper methods: `isAvailable()`, `getUtilizationPercentage()`

- **EquipmentFeature** (`app/Models/EquipmentFeature.php`)
  - Stores decorator-applied features (insurance, warranty, etc.)
  - Has expiry date tracking

- **EquipmentTransaction** (`app/Models/EquipmentTransaction.php`)
  - Tracks equipment checkouts, returns, and maintenance activities

### Views
- **Dashboard** (`resources/views/inventory/dashboard.blade.php`)
  - Main inventory overview with statistics and equipment list
  - Shows utilization metrics and status indicators

- **Create** (`resources/views/inventory/create.blade.php`)
  - Form for creating new equipment
  - UI for selecting decorator features (Factory Method & Decorator pattern demonstration)

- **Show** (`resources/views/inventory/show.blade.php`)
  - Detailed equipment view
  - Displays all decorator-applied features
  - Shows transaction history

- **Edit** (`resources/views/inventory/edit.blade.php`)
  - Form for updating equipment information

### Controller
- **InventoryController** (`app/Http/Controllers/InventoryController.php`)
  - Handles all inventory-related operations
  - Implements robust exception handling
  - Uses Factory Method pattern for equipment creation
  - Uses Decorator pattern for feature addition

## Exception Handling

**Location:** `app/Exceptions/`

**Custom Exceptions:**
1. **EquipmentException** - Base exception for all equipment-related errors
2. **EquipmentNotFoundException** - Thrown when equipment doesn't exist
3. **InsufficientQuantityException** - Thrown when checkout quantity exceeds available
4. **EquipmentStatusException** - Thrown when equipment status prevents operation

**Exception Handler:**
- Configured in `bootstrap/app.php`
- Provides user-friendly error messages
- Logs errors for debugging
- Returns appropriate HTTP status codes

**Features:**
- All exceptions are logged with context
- User-friendly error messages displayed in UI
- Detailed error information for debugging
- Graceful error recovery

## Database Schema

### Tables:
1. **equipment** - Main equipment table
   - Stores basic equipment information
   - Tracks quantity and availability
   - Includes status and maintenance dates

2. **equipment_features** - Decorator-applied features
   - Links to equipment via foreign key
   - Stores feature type, name, value, and expiry

3. **equipment_transactions** - Transaction history
   - Tracks checkouts, returns, maintenance
   - Links to equipment and users

## Routes

All routes are prefixed with `/inventory`:

- `GET /inventory` - Dashboard (index)
- `GET /inventory/create` - Create form
- `POST /inventory` - Store new equipment
- `GET /inventory/{id}` - Show equipment details
- `GET /inventory/{id}/edit` - Edit form
- `PUT /inventory/{id}` - Update equipment
- `DELETE /inventory/{id}` - Delete equipment
- `POST /inventory/{id}/checkout` - Checkout equipment
- `POST /inventory/{id}/return` - Return equipment

## Key Features

1. **Dashboard Statistics**
   - Total equipment count
   - Available equipment count
   - Maintenance equipment count
   - Total inventory value
   - Utilization rate

2. **Equipment Management**
   - Create equipment using Factory Method pattern
   - Add features using Decorator pattern
   - Track equipment status
   - Monitor utilization

3. **Transaction Tracking**
   - Checkout equipment
   - Return equipment
   - Track maintenance activities
   - User assignment

4. **Feature Management**
   - Insurance coverage tracking
   - Warranty management
   - Automated maintenance scheduling
   - Expiry date monitoring

## Usage Instructions

1. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

2. **Access the Dashboard:**
   Navigate to `/inventory` in your browser

3. **Create Equipment:**
   - Click "Add Equipment"
   - Select equipment type (triggers Factory Method pattern)
   - Optionally add features (triggers Decorator pattern)
   - Submit form

4. **View Equipment:**
   - Click on any equipment item to see details
   - View applied decorator features
   - Check transaction history

5. **Manage Equipment:**
   - Edit equipment information
   - Checkout/return equipment
   - Update status

## Design Pattern Benefits Demonstrated

### Factory Method Pattern:
- ✅ Encapsulates object creation
- ✅ Easy to extend with new equipment types
- ✅ Type-specific initialization
- ✅ Centralized creation logic

### Decorator Pattern:
- ✅ Dynamic feature addition
- ✅ Flexible feature combination
- ✅ No modification to base Equipment class
- ✅ Runtime feature application

## Error Handling

The system includes comprehensive error handling:
- Validation errors with user-friendly messages
- Custom exceptions for business logic errors
- Database transaction rollback on errors
- Detailed logging for debugging
- Graceful error recovery

## Future Enhancements

Potential improvements:
- Equipment categories and subcategories
- Barcode/QR code support
- Equipment reservation system
- Maintenance scheduling automation
- Equipment lifecycle tracking
- Reporting and analytics

