#!/bin/bash

find ./tests/Unit -name '*.php' | while read file; do
    if ! grep -q "namespace Tests\\\Unit;" "$file"; then
        echo "Corrigiendo namespace en: $file"
        sed -i '' '1s/^/<?php\n\nnamespace Tests\\\Unit;\n\n/' "$file"
    fi
done

