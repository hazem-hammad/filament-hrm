#!/bin/bash

# Source GitHub repository URL
REPO_URL="https://github.com/hazem-hammad/dockerizer"

# Prompt user to select Docker setup type
echo "Select Docker setup to clone:"
echo "1) PHP"
echo "2) Node"
read -p "Enter your choice (1 or 2): " SETUP_TYPE

# Set the source branch based on user choice
case $SETUP_TYPE in
  1)
    BRANCH="php"
    SETUP_FOLDER="php"
    ;;
  2)
    BRANCH="node"
    SETUP_FOLDER="node"
    ;;
  *)
    echo "Invalid choice. Please enter 1 or 2."
    exit 1
    ;;
esac

# Clone the selected branch from GitHub to the current directory
TEMP_DIR="temp_$SETUP_FOLDER"
git clone --branch "$BRANCH" "$REPO_URL" "$TEMP_DIR"

# Move docker-compose.yml to the root of the current directory
if [ -f "$TEMP_DIR/src/$SETUP_FOLDER/docker-compose.yml" ]; then
  mv "$TEMP_DIR/src/$SETUP_FOLDER/docker-compose.yml" ./
fi

# Move the rest of the files to a 'docker' folder in the current directory
if [ -d "$TEMP_DIR/src/$SETUP_FOLDER" ]; then
  mkdir -p docker
  mv "$TEMP_DIR/src/$SETUP_FOLDER"/* docker/
  rm -rf "$TEMP_DIR"
fi

# Success message
echo "Docker setup has been successfully cloned and added to the current directory."
