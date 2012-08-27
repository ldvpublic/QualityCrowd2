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

### Macros

#### include()
	<command> include(<file>)
Can be used instead of a commmand argument to pass the contents of `<file>` as the argument value.

#### variables
	<command> $<var>

## Properties

### answermode
	set answermode <continous|discrete|text>

### answers
	set answers "1: first answer; 2: second answer [;...]"

### mediaurl
	set mediaurl "http://example.com/path/"

### question
	set question <text>
Defines a question text that is displayed in all steps containing a question (`image`, `video`, ...) right above the answer form.

### skipvalidation
	set skipvalidation
Disables the validation of the answer values. The main purpose of this flag is to allow quick testing of the script during development.

### text
	set text <text>

### title
	set title <text>
Sets the title of a step page to `<text>`; will be displayed as page heading on all pages.

### videowidth
	set videowidth <number>

### videoheight
	set videoheight <number>


