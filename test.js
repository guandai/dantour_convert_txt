import * as vscode from 'vscode';

/**
 * Activates the extension.
 * @param context - The extension context.
 */
export function activate(context: vscode.ExtensionContext) {
  console.log('PHP Serialized String Formatter is now active!');

  let disposable = vscode.commands.registerCommand('phpSerializedStringFormatter.format', () => {
    const editor = vscode.window.activeTextEditor;
    if (!editor) {
      vscode.window.showInformationMessage('No active editor detected.');
      return;
    }

    const document = editor.document;
    const selection = editor.selection;

    // Get the text to format: either the selection or the entire document
    const text = selection.isEmpty ? document.getText() : document.getText(selection);

    // Format the serialized string
    const formattedText = formatSerializedString(text);

    if (formattedText) {
      editor.edit(editBuilder => {
        if (selection.isEmpty) {
          // Replace the entire document
          editBuilder.replace(new vscode.Range(document.positionAt(0), document.positionAt(text.length)), formattedText);
        } else {
          // Replace the selected text
          editBuilder.replace(selection, formattedText);
        }
      }).then(success => {
        if (success) {
          vscode.window.showInformationMessage('PHP serialized string formatted successfully.');
        } else {
          vscode.window.showErrorMessage('Failed to format PHP serialized string.');
        }
      });
    } else {
      vscode.window.showErrorMessage('Invalid PHP serialized string.');
    }
  });

  context.subscriptions.push(disposable);
}

/**
 * Deactivates the extension.
 */
export function deactivate() {}

/**
 * Formats a PHP serialized string by adding indentation and line breaks.
 * @param serialized - The serialized string to format.
 * @returns The formatted serialized string or null if invalid.
 */
function formatSerializedString(serialized: string): string | null {
  if (!isValidSerializedString(serialized)) {
    return null; // Not a serialized string
  }

  // Replace double double-quotes with a placeholder
  const placeholder = '__DOUBLE_QUOTE__';
  let tempSerialized = serialized.replace(/""/g, placeholder);

  let formatted = '';
  let indentLevel = 0;
  const length = tempSerialized.length;

  for (let i = 0; i < length; i++) {
    const char = tempSerialized[i];

    switch (char) {
      case '{':
        formatted += '{\n';
        indentLevel++;
        formatted += '\t'.repeat(indentLevel);
        break;

      case '}':
        formatted += ';\n';
        indentLevel--;
        formatted += '\t'.repeat(indentLevel) + '}';
        // Check if next character is not '}', to add newline and indentation
        if (i + 1 < length && tempSerialized[i + 1] !== '}') {
          formatted += '\n' + '\t'.repeat(indentLevel);
        }
        break;

      case ';':
        formatted += ';\n';
        // Check if next character is not '}'
        if (i + 1 < length && tempSerialized[i + 1] !== '}') {
          formatted += '\t'.repeat(indentLevel);
        }
        break;

      case ':':
        formatted += ':';
        break;

      default:
        formatted += char;
        break;
    }
  }

  // Restore the double double-quotes
  formatted = formatted.replace(new RegExp(placeholder, 'g'), '""');

  // Enclose in double quotes if originally enclosed
  if (serialized.startsWith('"') && serialized.endsWith('"')) {
    formatted = '"' + formatted.trim() + '"';
  }

  return formatted;
}

/**
 * Validates if the input string is a PHP serialized string.
 * Basic validation based on starting characters.
 * @param str - The string to validate.
 * @returns True if valid, else false.
 */
function isValidSerializedString(str: string): boolean {
  // Basic check for serialized string prefixes
  return /^(a|s|O|i|b|d):/.test(str.trim());
}
