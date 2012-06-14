# QC-Script

## Concept
QualitCrowd uses QC-Script to define so called test batches. A test batch is a pre definied sequence of steps for the use of media related online evaluation. A step is a webpage that is presented to the test subject and mostly consists of a piece of media (image, video, ...) and a corresponding question.

To define a batch create a QC-Script-File in `/batches/<batchName>/definition.qcs`. This batch will then be accessible via `http://qualitycrowd.example.com/<batchName>/<workerId>`

## Syntax

All identifiers (commands, property names and macros) use lower case characters only but nevertheless the parser is case sensitive!

### Commands
	<command> [<argument1>] [<argument2>] [...]
Each line in a QC-Script is considered as a statement; empty lines are possible. Both CR and LF (also CRLF etc.) are allowed as line endings.

Each statement begins with a command and may be followed by one or more arguments. The command and the arguments as well as the arguments among themselves are separated by whitespace (spaces or tabs). If a whitespace character is needed inside an argument value the whole argument has to be enclosed in double quotes (`"<value>"`). Therefore the double quote character itself has to be escaped: `"I am ""the"" value!"`. It might be a good idea to enclose all arguments containing natural language in double quotes.

### Comments
	# I am a comment
Lines starting with a number sign (#) are comments and removed before parsing.

## Command Reference

### Special Commands

#### meta
	meta <key> [<value>]

#### set
	set <key> [<value>]
The `set` command sets a property defined by the `<key>`-argument to the value specified by `<value>`. This property can be used by all further commands an its value will be set until a matching `unset`-command is processed.

#### unset
	unset <key>
Unsets the property with the passed `<key>`. If `all` is passed all properties will be unset.

#### var
	var <key> <value>
Sets an internal variable to `<value>`. To use this variable for example in a `set` command, use the following syntax:

	set title $titlevar

### Step Commands

The following commands will produce their own page which is presented to the test subject. Therefore a corresponding template has to exists under `/core/template/<command>.tpl.php`.

#### image
	image <file>

Used properties: `answermode`, `question`, `skipvalidation`, `title`

#### page
	page <pagecontent>
Displays a simple HTML-Page with the specified content and is particularly useful as a welcome page. It is recommended to use the `include()`-macro with this command.

Used properties: `title`

#### qualification
	qualification <batchId>

Used properties: `title`

#### question
	question

Used properties: `question`, `title`, ...

#### return
	return

Used properties: none

#### showtoken
	showtoken

Used properties: `title`

#### video
	image <file> [<file>]

Used properties: `answermode`, `question`, `skipvalidation`, `title`

## Properties

### answermode
	set answermode <continous|discrete|...>

### answers
	set answers "1: first answer; 2: second answer [;...]"

### question
	set question <text>
Defines a question text that is displayed in all steps containing a question (`image`, `video`, ...) right above the answer form.

### skipvalidation
	set skipvalidation
Disables the validation of the answer values. The main purpose of this flag is to allow quick testing of the script during development.

### title
	set title <text>
Sets the title of a step page to `<text>`; will be displayed as page heading on all pages.

## Macros

### include()
	<command> include(<file>)
Can be used instead of a commmand argument to pass the contents of `<file>` as the argument value.

### variables
	<command> $<var>
