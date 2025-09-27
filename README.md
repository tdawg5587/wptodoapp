# WordPress Todo Manager with MCP Integration

A WordPress-based todo management system designed to integrate with Docker Desktop via Model Context Protocol (MCP).

## Overview

This project combines:
- **WordPress** as the content management system for todos
- **Custom WordPress Plugin** (`todo-manager`) for todo management functionality
- **Custom WordPress Theme** (`todo-organizer`) for a clean, organized display
- **MCP Server** (planned) for Docker Desktop integration

## Features

- Custom post type for todo items
- REST API endpoints for external integrations
- Organizer-style theme optimized for todo management
- Integration ready for Docker Desktop MCP toolkit

## Installation

1. Set up WordPress in your local Apache environment
2. Activate the `todo-manager` plugin
3. Activate the `todo-organizer` theme
4. Configure the MCP server (when implemented)

## Project Structure

```
├── wp-content/
│   ├── plugins/
│   │   └── todo-manager/          # Custom todo management plugin
│   └── themes/
│       └── todo-organizer/        # Custom organizer theme
├── .gitignore
└── README.md
```

## Development

This project is under active development. The WordPress foundation is being built first, followed by the MCP server integration.

## API Endpoints

The todo-manager plugin will expose REST API endpoints at:
- `GET /wp-json/todo/v1/items` - Get all todos
- `POST /wp-json/todo/v1/items` - Create a new todo
- `PUT /wp-json/todo/v1/items/{id}` - Update a todo
- `DELETE /wp-json/todo/v1/items/{id}` - Delete a todo

## License

[Your chosen license]