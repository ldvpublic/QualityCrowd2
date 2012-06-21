CodeMirror.defineMode("qc-script", function() {
	var regexBuiltin = /^(meta|set|unset|var)$/m;
	var regexKeyword = /^(page|video|image|question|showtoken|qualification|return)$/m;
    var regexAttribute = /^(title|description|comment|answermode|answers|question|title|text|mediaurl)$/m;
    var regexSpecial = /^(include)$/m;

	return {
		token: function(stream) {
			if (stream.eatSpace()) return null;

            var sol = stream.sol();
			var ch = stream.next();

			if (ch == "#" && sol) {
				stream.skipToEnd();
				return "comment";
			}
			if (ch == '"') {
				stream.skipTo('"');
				return "string";
			}
			if (ch == '$') {
				stream.eatWhile(/\w/);
				return "variable";
			}

			if (/\w/.test(ch) && sol) {
				stream.eatWhile(/\w/);
				if (regexBuiltin.test(stream.current())) return "builtin";
			}

			if (/\w/.test(ch) && sol) {
				stream.eatWhile(/\w/);
				if (regexKeyword.test(stream.current())) return "keyword";
			}

            if (/\w/.test(ch)) {
                stream.eatWhile(/\w/);
                if (regexAttribute.test(stream.current())) return "atom";
            }

             if (/\w/.test(ch)) {
                stream.eatWhile(/\w/);
                if (regexSpecial.test(stream.current())) return "special";
            }

			return null;
		}
	};
});

CodeMirror.defineMIME("text/x-qc-script", "qc-script");
