Set shell = CreateObject("WScript.Shell")
shell.Run Chr(34) & CreateObject("Scripting.FileSystemObject").GetParentFolderName(WScript.ScriptFullName) & "\START_GREENPRINTING.bat" & Chr(34), 1, False
