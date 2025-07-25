<?php

declare(strict_types=1);

/**
 * Basic Type-Driven Metamorphosis Demo - Be Framework
 *
 * This example demonstrates the core concept of Type-Driven Metamorphosis
 * where objects carry their own destiny through typed properties.
 *
 * Key concepts shown:
 * - Objects that discover their own nature
 * - Union types as destiny maps
 * - Type-driven branching without if-statements
 * - The elimination of external control flow
 */

require_once __DIR__ . '/../poc/vendor/autoload.php';

use Be\Framework\Becoming;
use Ray\Di\Injector;
use Ray\Di\AbstractModule;
use Ray\Di\Di\Inject;
use Ray\InputQuery\Attribute\Input;
use Be\Framework\Be;

// =============================================================================
// DESTINY TYPES
// =============================================================================

/**
 * Represents data that was successfully processed
 */
final class Success
{
    public function __construct(
        public readonly string $data,
        public readonly string $processedBy = 'DataProcessor'
    ) {}
}

/**
 * Represents data that failed to process
 */
final class Failure
{
    public function __construct(
        public readonly string $reason,
        public readonly string $originalData
    ) {}
}

// =============================================================================
// SUPPORTING SERVICES
// =============================================================================

class DataValidator
{
    public function isValid(string $data): bool
    {
        // Simple validation: data must not be empty and not contain 'invalid'
        return !empty($data) && !str_contains(strtolower($data), 'invalid');
    }

    public function getErrors(string $data): string
    {
        if (empty($data)) {
            return 'Data cannot be empty';
        }
        if (str_contains(strtolower($data), 'invalid')) {
            return 'Data contains forbidden content';
        }
        return 'Unknown validation error';
    }
}

class DataProcessor
{
    public function process(string $data): string
    {
        return strtoupper($data) . ' [PROCESSED]';
    }
}

// =============================================================================
// TYPE-DRIVEN METAMORPHOSIS CHAIN
// =============================================================================

/**
 * Stage 1: Raw input data
 */
#[Be(BeingData::class)]
final class DataInput
{
    /**
     * Initializes a DataInput instance with the provided input value.
     *
     * @param string $value The raw input data to be processed.
     */
    public function __construct(
        #[Input] public readonly string $value
    ) {
        // Pure data container - no logic
    }
}

/**
 * Stage 2: Validation that discovers its own destiny
 *
 * This demonstrates Type-Driven Metamorphosis:
 * - The object asks itself: "Who am I?"
 * - It uses union types to express possible futures
 * - No external routing needed - the type determines the path
 */
#[Be([SuccessfulValidation::class, FailedValidation::class])]
final class BeingData
{
    /**
     * Constructs a BeingData by validating input data and determining its outcome.
     *
     * Uses the provided validator to check the input data. If valid, sets the `being` property to a Success instance with processed data; otherwise, sets it to a Failure instance with error details.
     */
    public function __construct(
        #[Input] string $value,                // Immanent
        #[Inject] DataValidator $validator,    // Transcendent
        #[Inject] DataProcessor $processor     // Transcendent
    ) {
        // The existential question: Who am I?
        $this->being = $validator->isValid($value)
            ? new Success($processor->process($value))
            : new Failure($validator->getErrors($value), $value);
    }

    // I carry my destiny within me
    public readonly Success|Failure $being;
}

/**
 * Success path - final transformation
 */
final class SuccessfulValidation
{
    public readonly string $message;
    public readonly \DateTimeImmutable $timestamp;

    public function __construct(
        #[Input] Success $being  // Parameter name matches the property name
    ) {
        $this->message = "Successfully processed: {$being->data}";
        $this->timestamp = new \DateTimeImmutable();
    }
}

/**
 * Failure path - final transformation
 */
final class FailedValidation
{
    public readonly string $message;
    public readonly string $originalData;
    public readonly \DateTimeImmutable $timestamp;

    public function __construct(
        #[Input] Failure $being  // Parameter name matches the property name
    ) {
        $this->message = "Processing failed: {$being->reason}";
        $this->originalData = $being->originalData;
        $this->timestamp = new \DateTimeImmutable();
    }
}

// =============================================================================
// DEPENDENCY INJECTION MODULE
// =============================================================================

class DemoModule extends AbstractModule
{
    protected function configure(): void
    {
        $this->bind(DataValidator::class);
        $this->bind(DataProcessor::class);
    }
}

// =============================================================================
// DEMO EXECUTION
// =============================================================================

echo "=== Type-Driven Metamorphosis Demo ===\n\n";

// Set up dependency injection
$injector = new Injector(new DemoModule());

// Create Becoming executor
$becoming = new Becoming($injector);

// Demo 1: Successful processing
echo "Demo 1: Valid Data\n";
echo "Input: 'hello world'\n";

$finalObject1 = $becoming(new DataInput('hello world'));
echo "Result: " . $finalObject1::class . "\n";
echo "Message: {$finalObject1->message}\n";
echo "Timestamp: " . $finalObject1->timestamp->format('Y-m-d H:i:s') . "\n\n";

// Demo 2: Failed processing
echo "Demo 2: Invalid Data\n";
echo "Input: 'invalid data'\n";

$finalObject2 = $becoming(new DataInput('invalid data'));
echo "Result: " . $finalObject2::class . "\n";
echo "Message: {$finalObject2->message}\n";
if (isset($finalObject2->originalData)) {
    echo "Original Data: {$finalObject2->originalData}\n";
}
echo "Timestamp: " . $finalObject2->timestamp->format('Y-m-d H:i:s') . "\n\n";

// Demo 3: Empty data
echo "Demo 3: Empty Data\n";
echo "Input: ''\n";

$finalObject3 = $becoming(new DataInput(''));
echo "Result: " . $finalObject3::class . "\n";
echo "Message: {$finalObject3->message}\n";
if (isset($finalObject3->originalData)) {
    echo "Original Data: '{$finalObject3->originalData}'\n";
}
echo "Timestamp: " . $finalObject3->timestamp->format('Y-m-d H:i:s') . "\n\n";

echo "=== Key Insights ===\n";
echo "1. No if-statements in the flow logic\n";
echo "2. Objects discover their own nature through the \$being property\n";
echo "3. Union types (Success|Failure) express all possible destinies\n";
echo "4. Framework automatically routes based on types\n";
echo "5. Each transformation is pure and predictable\n\n";

echo "=== Transformation Paths ===\n";
echo "DataInput -> BeingData -> SuccessfulValidation (if valid)\n";
echo "DataInput -> BeingData -> FailedValidation (if invalid)\n";
echo "\nThe path is determined by existential self-discovery, not external control!\n";
