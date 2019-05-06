## Translation
This Folder and the language Folders are created by calling
```bash
php yourconcretefolder/concrete/bin/concrete5.php c5:package-translate basic_table_package
```
This looks for all the occurences of the t() function and uses the contained string for the message keys in the .po files.
further doc in:  http://documentation.concrete5.org/localization  
you can add new languages by calling
```bash
php yourconcretefolder/concrete/bin/concrete5.php concrete5 c5:package-translate basic_table_package -l it_IT
```
Then you have the .po files for every language with the line, the message key and the msgstr.
put your transaltion in msgstr
```po
#: packages/basic_table_package/controller.php:28
msgid "BasicTablePackage"
msgstr ""
```
After you translated the strings, you have to compile the po files to mo files again by using:
```bash
php yourconcretefolder/concrete/bin/concrete5.php c5:package-translate basic_table_package
```