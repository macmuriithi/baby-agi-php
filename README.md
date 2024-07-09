# BabyAGI PHP Implementation

## Overview

This project is a PHP implementation of BabyAGI, inspired by Yohei Nakajima's original concept. It uses OpenAI's GPT models to create an autonomous task management system that can break down objectives into tasks, execute them, and generate new tasks based on the results.

## Features

- Task creation and management
- Automated task execution using OpenAI's GPT models
- Dynamic task generation based on execution results
- Task prioritization using AI
- Objective-driven task management

## Requirements

- PHP 7.4 or higher
- Composer
- OpenAI API key

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/babyagi-php.git
   cd babyagi-php
   ```

2. Install dependencies using Composer:
   ```bash
   composer require openai-php/client
   ```

3. Create a `.env` file in the project root and add your OpenAI API key:
   ```
   OPENAI_API_KEY=your_api_key_here
   ```

## Usage

### Basic Usage

1. Include the BabyAGI class in your PHP script:

   ```php
   require 'path/to/BabyAGI.php';
   ```

2. Create a new instance of BabyAGI with your API key and objective:

   ```php
   $api_key = getenv('OPENAI_API_KEY');
   $objective = 'Develop a comprehensive marketing strategy for a new eco-friendly product';
   $baby_agi = new BabyAGI($api_key, $objective);
   ```

3. Run BabyAGI with an initial task:

   ```php
   $baby_agi->run("Conduct market research on eco-friendly products");
   ```

### Complete Example

```php
<?php

require 'vendor/autoload.php';
require 'BabyAGI.php';

$api_key = getenv('OPENAI_API_KEY');
$objective = 'Develop a comprehensive marketing strategy for a new eco-friendly product';
$baby_agi = new BabyAGI($api_key, $objective);
$baby_agi->run("Conduct market research on eco-friendly products");
```

## Class Structure

### Task Class

The `Task` class represents individual tasks managed by BabyAGI.

```php
class Task {
    public $task_id;
    public $task_name;
    public $status;

    public function __construct($task_id, $task_name, $status = 'incomplete') {
        $this->task_id = $task_id;
        $this->task_name = $task_name;
        $this->status = $status;
    }
}
```

### BabyAGI Class

The `BabyAGI` class is the main class that manages the task execution process.

#### Properties

- `$task_list`: Array of current tasks
- `$task_id_counter`: Counter for assigning unique task IDs
- `$completed_tasks`: Array of completed tasks
- `$openai`: OpenAI API client instance
- `$objective`: The main objective of the task execution process

#### Methods

1. `__construct($api_key, $objective)`: Constructor that initializes the BabyAGI instance
2. `run($initial_task)`: Starts the task execution process
3. `add_task($task_name)`: Adds a new task to the task list
4. `get_next_task()`: Retrieves the next task to be executed
5. `execute_task($task)`: Executes a given task using the OpenAI API
6. `process_task_result($task, $result)`: Processes the result of a task execution
7. `generate_new_tasks($result)`: Generates new tasks based on the result of a task execution
8. `prioritize_tasks()`: Prioritizes the current task list

## Customization

You can customize the behavior of BabyAGI by modifying the following methods in the `BabyAGI` class:

- `execute_task`: Change how tasks are executed
- `generate_new_tasks`: Adjust the logic for creating new tasks
- `prioritize_tasks`: Modify the task prioritization algorithm

## Error Handling

The current implementation does not include extensive error handling. In a production environment, you should add try-catch blocks and proper error logging, especially around API calls and file operations.

## API Usage and Limitations

This implementation uses OpenAI's API, which may incur costs depending on your usage and plan. Be aware of the following:

- API rate limits
- Token usage limits
- Costs associated with API calls

Refer to OpenAI's documentation for the most up-to-date information on API usage and limitations.

## Best Practices

1. **Objective Definition**: Define clear and specific objectives for best results.
2. **Initial Task**: Start with a well-defined initial task that aligns with the objective.
3. **API Key Security**: Always keep your API key secure and never commit it to version control.
4. **Monitoring**: Monitor the task execution process and API usage to optimize performance and costs.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgements

This project is inspired by Yohei Nakajima's BabyAGI concept and uses OpenAI's GPT models for natural language processing.
