#!/usr/bin/env python3
"""Formata PHP editado pelo agente com o Pint do projeto, se o binário existir."""

import json
import os
import subprocess
import sys


def file_path(data):
    for key in ("file_path", "filePath", "path"):
        value = data.get(key)
        if isinstance(value, str) and value:
            return value
    return ""


def main():
    raw = sys.stdin.read()
    try:
        data = json.loads(raw) if raw.strip() else {}
    except json.JSONDecodeError:
        return

    path = file_path(data)
    if not path.endswith(".php"):
        return

    root = os.getcwd()
    try:
        full = os.path.realpath(path)
        if os.path.commonpath([root, full]) != os.path.realpath(root):
            return
    except ValueError:
        return

    pint = os.path.join(root, "vendor", "bin", "pint")
    if not os.path.isfile(pint) or not os.access(pint, os.X_OK):
        return

    subprocess.run(
        [pint, full],
        stdout=subprocess.DEVNULL,
        stderr=subprocess.DEVNULL,
        check=False,
    )


if __name__ == "__main__":
    main()
