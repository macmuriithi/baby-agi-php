<?php

require 'vendor/autoload.php';

use OpenAI\Client;

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

class BabyAGI {
    private $task_list = [];
    private $task_id_counter = 1;
    private $completed_tasks = [];
    private $openai;
    private $objective;

    public function __construct($api_key, $objective) {
        $this->openai = OpenAI::client($api_key);
        $this->objective = $objective;
    }

    public function run($initial_task) {
        $this->add_task($initial_task);

        while (!empty($this->task_list)) {
            $current_task = $this->get_next_task();
            $result = $this->execute_task($current_task);
            $this->process_task_result($current_task, $result);
            $this->prioritize_tasks();
        }

        echo "All tasks completed. Objective: {$this->objective}\n";
        echo "Completed tasks:\n";
        foreach ($this->completed_tasks as $task) {
            echo "- {$task->task_name}\n";
        }
    }

    private function add_task($task_name) {
        $new_task = new Task($this->task_id_counter++, $task_name);
        $this->task_list[] = $new_task;
    }

    private function get_next_task() {
        return array_shift($this->task_list);
    }

    private function execute_task($task) {
        echo "Executing task: {$task->task_name}\n";
        
        $prompt = "Complete the following task: {$task->task_name}\n" .
                  "This task is part of the following objective: {$this->objective}\n" .
                  "Provide a detailed response.";

        $response = $this->openai->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 500
        ]);

        return $response['choices'][0]['text'];
    }

    private function process_task_result($task, $result) {
        echo "Task result: $result\n";
        $task->status = 'complete';
        $this->completed_tasks[] = $task;

        $new_tasks = $this->generate_new_tasks($result);
        foreach ($new_tasks as $new_task) {
            $this->add_task($new_task);
        }
    }

    private function generate_new_tasks($result) {
        $prompt = "Based on the following task result, generate up to 3 new tasks that will help achieve the objective: {$this->objective}\n" .
                  "Task result: $result\n" .
                  "New tasks:";

        $response = $this->openai->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 200
        ]);

        $new_tasks_text = $response['choices'][0]['text'];
        $new_tasks = array_filter(array_map('trim', explode("\n", $new_tasks_text)));
        
        return $new_tasks;
    }

    private function prioritize_tasks() {
        $task_names = array_map(function($task) { return $task->task_name; }, $this->task_list);
        $tasks_string = implode("\n", $task_names);

        $prompt = "Prioritize the following tasks based on their importance and urgency for achieving the objective: {$this->objective}\n" .
                  "Tasks:\n$tasks_string\n" .
                  "Provide the prioritized list of task names, one per line.";

        $response = $this->openai->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $prompt,
            'max_tokens' => 200
        ]);

        $prioritized_tasks = array_filter(array_map('trim', explode("\n", $response['choices'][0]['text'])));
        
        $this->task_list = array_values(array_filter($this->task_list, function($task) use ($prioritized_tasks) {
            return in_array($task->task_name, $prioritized_tasks);
        }));

        usort($this->task_list, function($a, $b) use ($prioritized_tasks) {
            return array_search($a->task_name, $prioritized_tasks) - array_search($b->task_name, $prioritized_tasks);
        });
    }
}
