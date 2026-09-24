#!/usr/bin/env python3
"""Pede confirmação ou bloqueia comandos destrutivos no shell do agente."""

import json
import re
import sys

ASK = (
    (re.compile(r"\bgit\s+push\b.*(?:--force(?:-with-lease)?|-f)\b", re.I), "git push --force"),
    (re.compile(r"\bgit\s+reset\s+--hard\b", re.I), "git reset --hard"),
    (re.compile(r"\bgit\s+clean\b[^\n]*\s-[a-zA-Z]*f", re.I), "git clean -f"),
    (re.compile(r"\bartisan\s+migrate:(?:fresh|reset)\b", re.I), "migrate destrutivo"),
    (re.compile(r"\bartisan\s+db:wipe\b", re.I), "db:wipe"),
)

# rm -rf contra a raiz do sistema, o home, o diretório atual ou um glob solto.
DENY_RM = re.compile(
    r"\brm\s+-[a-zA-Z]*r[a-zA-Z]*f[a-zA-Z]*\s+(?:/\S*|~\S*|\.(?:\s|$)|/\s|\*)",
)


def emit(permission, reason):
    json.dump(
        {
            "permission": permission,
            "user_message": reason,
            "agent_message": reason,
        },
        sys.stdout,
    )


def main():
    raw = sys.stdin.read()
    try:
        data = json.loads(raw) if raw.strip() else {}
    except json.JSONDecodeError:
        emit("allow", "")
        return

    command = data.get("command") or ""
    if not isinstance(command, str):
        command = ""

    if DENY_RM.search(command):
        emit("deny", "Bloqueado: rm -rf em caminho amplo (/ , ~ , . ou *).")
        return

    for pattern, label in ASK:
        if pattern.search(command):
            emit("ask", "Confirme antes de executar: %s." % label)
            return

    emit("allow", "")


if __name__ == "__main__":
    main()
