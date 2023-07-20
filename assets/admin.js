jQuery(document).ready(function ($) {
   var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
   editorSettings.codemirror = _.extend(
      {},
      editorSettings.codemirror,
      {
         mode: 'application/json',
         indentWithTabs: false,
         tabSize: 3,
         lineNumbers: true,
         lineWrapping: true,
         autoCloseBrackets: true,
         matchBrackets: true,
         lint: true,
         styleActiveLine: true,
         // theme: '3024-night'
         theme: 'monokai'
      }
   );
   var editor = wp.codeEditor.initialize($('.code-highlight textarea'), editorSettings);

   // Beautify JSON on page load
   // try {
   //    var jsonStr = editor.codemirror.getValue();
   //    var jsonObj = JSON.parse(jsonStr);
   //    var beautifiedJsonStr = JSON.stringify(jsonObj, null, 3);
   //    editor.codemirror.setValue(beautifiedJsonStr);
   // } catch (e) {
   //    console.error("Invalid JSON");
   // }

   editor.codemirror.on('blur', function(){
      var totalLines = editor.codemirror.lineCount();
      var totalChars = editor.codemirror.getTextArea().value.length;
      editor.codemirror.autoFormatRange({line:0, ch:0}, {line:totalLines, ch:totalChars});
   });

   editor.codemirror.on('changes', function(cm){
      cm.setSize(null, cm.getScrollInfo().height);
   });

   editor.codemirror.setSize(null, editor.codemirror.getScrollInfo().height);
});
