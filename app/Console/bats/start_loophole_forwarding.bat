@echo off
cd /d "C:\loophole"
"C:\loophole\loophole.exe" account login
"C:\loophole\loophole.exe" http 80 --hostname tumbashka-easy-tutor
:loop
timeout /t 60
goto loop