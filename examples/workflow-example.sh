#!/bin/bash
# Example: Using Phantom.ai Workflow Automation
# This script demonstrates the complete workflow for processing a WordPress development task

set -e

echo "==================================="
echo "Phantom.ai Workflow Example"
echo "==================================="
echo ""

# Example task
TASK="Create a custom WordPress block in blocks/testimonial-slider/block.json that displays testimonials with slider functionality"

echo "Task: $TASK"
echo ""

echo "Step 1: Classify the task"
echo "-----------------------------------"
./phantom-ai/phantom-cli classify "$TASK"
echo ""

echo "Step 2: Process the task through workflow"
echo "-----------------------------------"
./phantom-ai/phantom-cli process "$TASK"
echo ""

echo "Step 3: View current statistics"
echo "-----------------------------------"
./phantom-ai/phantom-cli stats
echo ""

echo "==================================="
echo "Workflow example complete!"
echo "==================================="
echo ""
echo "For a high-tier task, you would:"
echo "1. Run: ./phantom-ai/phantom-cli copilot <task-id>"
echo "2. Copy the generated prompt"
echo "3. Paste it into GitHub Copilot"
echo "4. Copilot generates the code"
echo "5. Store metadata with the results"
echo ""
