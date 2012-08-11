/*
Script: Moo.js
	My Object Oriented javascript.

License:
	MIT-style license.

Moo.MooTools Copyright:
	copyright (c) 2007 Valerio Proietti, <http://mad4milk.net>

Moo.MooTools Credits:
	- Moo.Class is slightly based on Base.js <http://dean.edwards.name/weblog/2006/03/base/> (c) 2006 Dean Edwards, License <http://creativecommons.org/licenses/LGPL/2.1/>
	- Some functions are inspired by those found in prototype.js <http://prototype.conio.net/> (c) 2005 Sam Stephenson sam [at] conio [dot] net, MIT-style license
	- Documentation by Aaron Newton (aaron.newton [at] cnet [dot] com) and Valerio Proietti.
*/

/*
Object: Moo
	The root namespace object for all replaced classes and methods

	Contains:
		MooTools, Class, $type, $merge, $mergeClass, $extend, $native, Abstract,
		Window, Document, $chk, $pick, $random, $time, $clear, Chain, Events,
		Options, $A, $each, Element, Elements, $, $$, Garbage, Event, $E, $ES,
		Filters, Fx, Drag, XHR, Ajax, Cookie, Json, Asset, Hash, $H, Color, $RGB,
		$HSB, Scroller, Slider, SmoothScroll, Sortables, Tips, Group, Accordion
*/

var Moo = {};


Moo.MooTools = {
	version: '1.1dev'
};

/*
Moo.Class: Moo.Class
	The base class object of the <http://mootools.net> framework.
	Creates a new class, its initialize method will fire upon class instantiation.
	Initialize wont fire on instantiation when you pass *null*.

Arguments:
	properties - the collection of properties that apply to the class.

Example:
	(start code)
	var Cat = new Moo.Class({
		initialize: function(name){
			this.name = name;
		}
	});
	var myCat = new Cat('Micia');
	alert(myCat.name); //alerts 'Micia'
	(end)
*/

Moo.Class = function(properties){
	var klass = function(){
		if (arguments[0] !== null && this.initialize && Moo.$type(this.initialize) == 'function') return this.initialize.apply(this, arguments);
		else return this;
	};
	Moo.$extend(klass, this);
	klass.prototype = properties;
	return klass;
};

/*
Property: empty
	Returns an empty function
*/

Moo.Class.empty = function(){};

Moo.Class.prototype = {

	/*
	Property: extend
		Returns the copy of the Moo.Class extended with the passed in properties.

	Arguments:
		properties - the properties to add to the base class in this new Moo.Class.

	Example:
		(start code)
		var Animal = new Moo.Class({
			initialize: function(age){
				this.age = age;
			}
		});
		var Cat = Animal.extend({
			initialize: function(name, age){
				this.parent(age); //will call the previous initialize;
				this.name = name;
			}
		});
		var myCat = new Cat('Micia', 20);
		alert(myCat.name); //alerts 'Micia'
		alert(myCat.age); //alerts 20
		(end)
	*/

	extend: function(properties){
		var proto = new this(null);
		for (var property in properties){
			var pp = proto[property];
			proto[property] = Moo.$mergeClass(pp, properties[property]);
		}
		return new Moo.Class(proto);
	},

	/*
	Property: implement
		Implements the passed in properties to the base Moo.Class prototypes, altering the base class, unlike <Moo.Class.extend>.

	Arguments:
		properties - the properties to add to the base class.

	Example:
		(start code)
		var Animal = new Moo.Class({
			initialize: function(age){
				this.age = age;
			}
		});
		Animal.implement({
			setName: function(name){
				this.name = name
			}
		});
		var myAnimal = new Animal(20);
		myAnimal.setName('Micia');
		alert(myAnimal.name); //alerts 'Micia'
		(end)
	*/

	implement: function(properties){
		Moo.$extend(this.prototype, properties);
	}

};

/* Section: Utility Functions */

/*
Function: Moo.$type
	Returns the type of object that matches the element passed in.

Arguments:
	obj - the object to inspect.

Example:
	>var myString = 'hello';
	>Moo.$type(myString); //returns "string"

Returns:
	'element' - if obj is a DOM element node
	'textnode' - if obj is a DOM text node
	'whitespace' - if obj is a DOM whitespace node
	'array' - if obj is an array
	'object' - if obj is an object
	'string' - if obj is a string
	'number' - if obj is a number
	'boolean' - if obj is a boolean
	'function' - if obj is a function
	'regexp' - if obj is a regular expression
	false - (boolean) if the object is not defined or none of the above.
*/

Moo.$type = function(obj){
	if (obj == undefined) return false;
	var type = typeof obj;
	if (type == 'object'){
		if (obj.htmlElement) return 'element';
		if (obj.push) return 'array';
		if (obj.nodeName){
			switch(obj.nodeType){
				case 1: return 'element';
				case 3: return obj.nodeValue.test(/\S/) ? 'textnode' : 'whitespace';
			}
		}
	}
	if ((type == 'object' || type == 'function') && obj.exec) return 'regexp';
	return type;
};

/*
Function: Moo.$merge
	merges a number of objects recursively without referencing them or their sub-objects.

Arguments:
	any number of objects.

Example:
	>var mergedObj = Moo.$merge(obj1, obj2, obj3);
	>//obj1, obj2, and obj3 are unaltered
*/

Moo.$merge = function(){
	var mix = {};
	for (var i = 0; i < arguments.length; i++){
		for (var property in arguments[i]){
			var ap = arguments[i][property];
			var mp = mix[property];
			if (mp && Moo.$type(ap) == 'object' && Moo.$type(mp) == 'object') mix[property] = Moo.$merge(mp, ap);
			else mix[property] = ap;
		}
	}
	return mix;
};

//internal

Moo.$mergeClass = function(previous, current){
	if (previous && previous != current){
		var ptype = Moo.$type(previous);
		var ctype = Moo.$type(current);
		if (ptype == 'function' && ctype == 'function'){
			var merged = function(){
				this.parent = arguments.callee.parent;
				return current.apply(this, arguments);
			};
			merged.parent = previous;
			return merged;
		} else if (ptype == 'object' && ctype == 'object'){
			return Moo.$merge(previous, current);
		}
	}
	return current;
};

/*
Function: Moo.$extend
	Copies all the properties from the second passed object to the first passed Object.
	If you do myWhatever.extend = Moo.$extend the first parameter will become myWhatever, and your extend function will only need one parameter.

Example:
	(start code)
	var firstOb = {
		'name': 'John',
		'lastName': 'Doe'
	};
	var secondOb = {
		'age': '20',
		'sex': 'male',
		'lastName': 'Dorian'
	};
	Moo.$extend(firstOb, secondOb);
	//firstOb will become:
	{
		'name': 'John',
		'lastName': 'Dorian',
		'age': '20',
		'sex': 'male'
	};
	(end)

Returns:
	The first object, extended.
*/

Moo.$extend = Object.extend = function(){
	var args = arguments;
	if (!args[1]) args = [this, args[0]];
	for (var property in args[1]) args[0][property] = args[1][property];
	return args[0];
};

/*
Function: Moo.$native
	Will add a .extend method to the objects passed as a parameter, but the property passed in will be copied to the object's prototype only if non previously existent.
	Its handy if you dont want the .extend method of an object to overwrite existing methods.
	Used automatically in mootools to implement Array/String/Function/Number methods to browser that dont support them whitout manual checking.

Arguments:
	a number of classes/native javascript objects

*/

Moo.$native = Object.Native = function(){
	for (var i = 0; i < arguments.length; i++) arguments[i].extend = Moo.$native.extend;
};

Moo.$native.extend = function(props){
	for (var prop in props){
		if (!this.prototype[prop]) this.prototype[prop] = props[prop];
	}
};

Moo.$native(Function, Array, String, Number, Moo.Class);

/*
Script: Utility.js
	Contains Utility functions

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Abstract
	Moo.Abstract class, to be used as singleton. Will add .extend to any object

Arguments:
	an object
	
Returns:
	the object with an .extend property, equivalent to <Moo.$extend>.
*/

Moo.Abstract = function(obj){
	obj = obj || {};
	obj.extend = Moo.$extend;
	return obj;
};

//window, document

Moo.Window = new Moo.Abstract(window);
Moo.Document = new Moo.Abstract(document);
document.head = document.getElementsByTagName('head')[0];

/* Section: Utility Functions */

/*
Function: Moo.$chk
	Returns true if the passed in value/object exists or is 0, otherwise returns false.
	Useful to accept zeroes.

Arguments:
	obj - object to inspect
*/

Moo.$chk = function(obj){
	return !!(obj || obj === 0);
};

/*
Function: Moo.$pick
	Returns the first object if defined, otherwise returns the second.

Arguments:
	obj - object to test
	picked - the default to return

Example:
	(start code)
		function say(msg){
			alert(Moo.$pick(msg, 'no meessage supplied'));
		}
	(end)
*/

Moo.$pick = function(obj, picked){
	return (obj != undefined) ? obj : picked;
};

/*
Function: Moo.$random
	Returns a random integer number between the two passed in values.

Arguments:
	min - integer, the minimum value (inclusive).
	max - integer, the maximum value (inclusive).

Returns:
	a random integer between min and max.
*/

Moo.$random = function(min, max){
	return Math.floor(Math.random() * (max - min + 1) + min);
};

/*
Function: Moo.$time
	Returns the current timestamp

Returns:
	a timestamp integer.
*/

Moo.$time = function(){
	return new Date().getTime();
};

/*
Function: Moo.$clear
	clears a timeout or an Interval.

Returns:
	null

Arguments:
	timer - the setInterval or setTimeout to clear.

Example:
	>var myTimer = myFunction.delay(5000); //wait 5 seconds and execute my function.
	>myTimer = Moo.$clear(myTimer); //nevermind

See also:
	<Function.delay>, <Function.periodical>
*/

Moo.$clear = function(timer){
	clearTimeout(timer);
	clearInterval(timer);
	return null;
};

/*
Moo.Class: window
	Some properties are attached to the window object by the browser detection.

Properties:
	window.ie - will be set to true if the current browser is internet explorer (any).
	window.ie6 - will be set to true if the current browser is internet explorer 6.
	window.ie7 - will be set to true if the current browser is internet explorer 7.
	window.khtml - will be set to true if the current browser is Safari/Konqueror.
	window.gecko - will be set to true if the current browser is Mozilla/Gecko.
*/

if (window.ActiveXObject) window.ie = window[window.XMLHttpRequest ? 'ie7' : 'ie6'] = true;
else if (document.childNodes && !document.all && !navigator.taintEnabled) window.khtml = true;
else if (document.getBoxObjectFor != null) window.gecko = true;
window.xpath = !!(document.evaluate);

//htmlelement

if (typeof HTMLElement == 'undefined'){
	var HTMLElement = Moo.Class.empty;
	if (window.khtml) document.createElement("iframe"); //fixes safari
	HTMLElement.prototype = (window.khtml) ? window["[[DOMElement.prototype]]"] : {};
}
HTMLElement.prototype.htmlElement = true;

//enables background image cache for internet explorer 6

if (window.ie6) try {document.execCommand("BackgroundImageCache", false, true);} catch(e){};

/*
Script: Common.js
	Contains common implementations for custom classes. In Mootools is implemented in <Moo.Ajax>, <Moo.XHR> and <Moo.Fx.Base> and many more.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Chain
	An "Utility" Moo.Class. Its methods can be implemented with <Moo.Class.implement> into any <Moo.Class>.
	Currently implemented in <Moo.Fx.Base>, <Moo.XHR> and <Moo.Ajax>. In <Moo.Fx.Base> for example, is used to execute a list of function, one after another, once the effect is completed.
	The functions will not be fired all togheter, but one every completion, to create custom complex animations.

Example:
	(start code)
	var myFx = new Moo.Fx.Style('element', 'opacity');

	myFx.start(1,0).chain(function(){
		myFx.start(0,1);
	}).chain(function(){
		myFx.start(1,0);
	}).chain(function(){
		myFx.start(0,1);
	});
	//the element will appear and disappear three times
	(end)
*/

Moo.Chain = new Moo.Class({

	/*
	Property: chain
		adds a function to the Moo.Chain instance stack.

	Arguments:
		fn - the function to append.
	*/

	chain: function(fn){
		this.chains = this.chains || [];
		this.chains.push(fn);
		return this;
	},

	/*
	Property: callChain
		Executes the first function of the Moo.Chain instance stack, then removes it. The first function will then become the second.
	*/

	callChain: function(){
		if (this.chains && this.chains.length) this.chains.shift().delay(10, this);
	},

	/*
	Property: clearChain
		Clears the stack of a Moo.Chain instance.
	*/

	clearChain: function(){
		this.chains = [];
	}

});

/*
Moo.Class: Moo.Events
	An "Utility" Moo.Class. Its methods can be implemented with <Moo.Class.implement> into any <Moo.Class>.

	In <Moo.Fx.Base> Moo.Class, for example, is used to give the possibility add any number of functions to the Effects events, like onComplete, onStart, onCancel.

Example:
	(start code)
	var myFx = new Moo.Fx.Style('element', 'opacity').addEvent('onComplete', function(){
		alert('the effect is completed');
	}).addEvent('onComplete', function(){
		alert('I told you the effect is completed');
	});

	myFx.start(0,1);
	//upon completion it will display the 2 alerts, in order.
	(end)

	Implementing:
		This class can be implemented into other classes to add the functionality to them.
		Goes well with the <Moo.Options> class.

	Example:
		(start code)
		var Widget = new Moo.Class({
			initialize: function(){},
			finish: function(){
				this.fireEvent('onComplete');
			}
		});
		Widget.implement(new Moo.Events);
		//later...
		var myWidget = new Widget();
		myWidget.addEvent('onComplete', myfunction);
		(end)
*/

Moo.Events = new Moo.Class({

	/*
	Property: addEvent
		adds an event to the stack of events of the Moo.Class instance.

	Arguments:
		type - string; the event name (e.g. 'onComplete')
		fn - function to execute
	*/

	addEvent: function(type, fn){
		if (fn != Moo.Class.empty){
			this.$events = this.$events || {};
			this.$events[type] = this.$events[type] || [];
			this.$events[type].include(fn);
		}
		return this;
	},

	/*
	Property: fireEvent
		fires all events of the specified type in the Moo.Class instance.

	Arguments:
		type - string; the event name (e.g. 'onComplete')
		args - array or single object; arguments to pass to the function; if more than one argument, must be an array
		delay - (integer) delay (in ms) to wait to execute the event

	Example:
	(start code)
	var Widget = new Moo.Class({
		initialize: function(arg1, arg2){
			...
			this.fireEvent("onInitialize", [arg1, arg2], 50);
		}
	});
	Widget.implement(new Moo.Events);
	(end)
	*/

	fireEvent: function(type, args, delay){
		if (this.$events && this.$events[type]){
			this.$events[type].each(function(fn){
				fn.create({'bind': this, 'delay': delay, 'arguments': args})();
			}, this);
		}
		return this;
	},

	/*
	Property: removeEvent
		removes an event from the stack of events of the Moo.Class instance.

	Arguments:
		type - string; the event name (e.g. 'onComplete')
		fn - function that was added
	*/

	removeEvent: function(type, fn){
		if (this.$events && this.$events[type]) this.$events[type].remove(fn);
		return this;
	}

});

/*
Moo.Class: Moo.Options
	An "Utility" Moo.Class. Its methods can be implemented with <Moo.Class.implement> into any <Moo.Class>.
	Used to automate the options settings, also adding Moo.Class <Moo.Events> when the option begins with on.

	Example:
		(start code)
		var Widget = new Moo.Class({
			options: {
				color: '#fff',
				size: {
					width: 100
					height: 100
				}
			},
			initialize: function(options){
				this.setOptions(options);
			}
		});
		Widget.implement(new Moo.Options);
		//later...
		var myWidget = new Widget({
			color: '#f00',
			size: {
				width: 200
			}
		});
		//myWidget.options = {color: #f00, size: {width: 200, height: 100}}
		(end)


*/

Moo.Options = new Moo.Class({

	/*
	Property: setOptions
		sets this.options

	Arguments:
		defaults - object; the default set of options
		options - object; the user entered options. can be empty too.

	Note:
		if your Moo.Class has <Moo.Events> implemented, every option beginning with on, followed by a capital letter (onComplete) becomes an Moo.Class instance event.
	*/

	setOptions: function(){
		var args = (arguments.length == 1) ? [this.options, arguments[0]] : arguments;
		this.options = Moo.$merge.apply(this, args);
		if (this.addEvent){
			for (var option in this.options){
				if ((Moo.$type(this.options[option]) == 'function') && option.test(/^on[A-Z]/)) this.addEvent(option, this.options[option]);
			}
		}
		return this;
	}

});

/*
Script: Array.js
	Contains Array prototypes, <Moo.$A>, <Moo.$each>

License:
	MIT-style license.
*/

/*
Moo.Class: Array
	A collection of The Array Object prototype methods.
*/

//custom methods

Array.extend({

	/*
	Property: forEach
		Iterates through an array; This method is only available for browsers without native *forEach* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:forEach>

		*forEach* executes the provided function (callback) once for each element present in the array. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>['apple','banana','lemon'].each(function(item, index) {
		>	alert(index + " = " + item); //alerts "0 = apple" etc.
		>}, bindObj); //optional second arg for binding, not used here
	*/

	forEach: function(fn, bind){
		for (var i = 0, j = this.length; i < j; i++) fn.call(bind, this[i], i, this);
	},

	/*
	Property: filter
		This method is provided only for browsers without native *filter* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Objects:Array:filter>

		*filter* calls a provided callback function once for each element in an array, and constructs a new array of all the values for which callback returns a true value. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values. Array elements which do not pass the callback test are simply skipped, and are not included in the new array.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>var biggerThanTwenty = [10,3,25,100].filter(function(item, index) {
		> return item > 20;
		>});
		>//biggerThanTwenty = [25,100]
	*/

	filter: function(fn, bind){
		var results = [];
		for (var i = 0, j = this.length; i < j; i++){
			if (fn.call(bind, this[i], i, this)) results.push(this[i]);
		}
		return results;
	},

	/*
	Property: map
		This method is provided only for browsers without native *map* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:map>

		*map* calls a provided callback function once for each element in an array, in order, and constructs a new array from the results. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>var timesTwo = [1,2,3].map(function(item, index){
		> return item*2;
		>});
		>//timesTwo = [2,4,6];
	*/

	map: function(fn, bind){
		var results = [];
		for (var i = 0, j = this.length; i < j; i++) results[i] = fn.call(bind, this[i], i, this);
		return results;
	},

	/*
	Property: every
		This method is provided only for browsers without native *every* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:every>

		*every* executes the provided callback function once for each element present in the array until it finds one where callback returns a false value. If such an element is found, the every method immediately returns false. Otherwise, if callback returned a true value for all elements, every will return true. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>var areAllBigEnough = [10,4,25,100].every(function(item, index){
		> return item > 20;
		>});
		>//areAllBigEnough = false
	*/

	every: function(fn, bind){
		for (var i = 0, j = this.length; i < j; i++){
			if (!fn.call(bind, this[i], i, this)) return false;
		}
		return true;
	},

	/*
	Property: some
		This method is provided only for browsers without native *some* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:some>

		*some* executes the callback function once for each element present in the array until it finds one where callback returns a true value. If such an element is found, some immediately returns true. Otherwise, some returns false. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>var isAnyBigEnough = [10,4,25,100].some(function(item, index){
		> return item > 20;
		>});
		>//isAnyBigEnough = true
	*/

	some: function(fn, bind){
		for (var i = 0, j = this.length; i < j; i++){
			if (fn.call(bind, this[i], i, this)) return true;
		}
		return false;
	},

	/*
	Property: indexOf
		This method is provided only for browsers without native *indexOf* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:indexOf>

		*indexOf* compares a search element to elements of the Array using strict equality (the same method used by the ===, or triple-equals, operator).

	Arguments:
		item - any type of object; element to locate in the array
		from - integer; optional; the index of the array at which to begin the search (defaults to 0)

	Example:
		>['apple','lemon','banana'].indexOf('lemon'); //returns 1
		>['apple','lemon'].indexOf('banana'); //returns -1
	*/

	indexOf: function(item, from){
		var len = this.length;
		for (var i = (from < 0) ? Math.max(0, len + from) : from || 0; i < len; i++){
			if (this[i] === item) return i;
		}
		return -1;
	},

	/*
	Property: each
		Same as <Array.forEach>.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - optional, the object that the "this" of the function will refer to.

	Example:
		>var Animals = ['Cat', 'Dog', 'Coala'];
		>Animals.each(function(animal){
		>	document.write(animal)
		>});
	*/

	/*
	Property: copy
		returns a copy of the array.

	Returns:
		a new array which is a copy of the current one.

	Arguments:
		start - integer; optional; the index where to start the copy, default is 0. If negative, it is taken as the offset from the end of the array.
		length - integer; optional; the number of elements to copy. By default, copies all elements from start to the end of the array.

	Example:
		>var letters = ["a","b","c"];
		>var copy = letters.copy();		// ["a","b","c"] (new instance)
	*/

	copy: function(start, length){
		start = start || 0;
		if (start < 0) start = this.length + start;
		length = length || (this.length - start);
		var newArray = [];
		for (var i = 0; i < length; i++) newArray[i] = this[start++];
		return newArray;
	},

	/*
	Property: remove
		Removes all occurrences of an item from the array.

	Arguments:
		item - the item to remove

	Returns:
		the Array with all occurrences of the item removed.

	Example:
		>["1","2","3","2"].remove("2") // ["1","3"];
	*/

	remove: function(item){
		var i = 0;
		var len = this.length;
		while (i < len){
			if (this[i] === item){
				this.splice(i, 1);
				len--;
			} else {
				i++;
			}
		}
		return this;
	},

	/*
	Property: contains
		Tests an array for the presence of an item.

	Arguments:
		item - the item to search for in the array.
		from - integer; optional; the index at which to begin the search, default is 0. If negative, it is taken as the offset from the end of the array.

	Returns:
		true - the item was found
		false - it wasn't

	Example:
		>["a","b","c"].contains("a"); // true
		>["a","b","c"].contains("d"); // false
	*/

	contains: function(item, from){
		return this.indexOf(item, from) != -1;
	},

	/*
	Property: associate
		Creates an object with key-value pairs based on the array of keywords passed in
		and the current content of the array.

	Arguments:
		keys - the array of keywords.

	Example:
		(start code)
		var Animals = ['Cat', 'Dog', 'Coala', 'Lizard'];
		var Speech = ['Miao', 'Bau', 'Fruuu', 'Mute'];
		var Speeches = Animals.associate(Speech);
		//Speeches['Miao'] is now Cat.
		//Speeches['Bau'] is now Dog.
		//...
		(end)
	*/

	associate: function(keys){
		var obj = {}, length = Math.min(this.length, keys.length);
		for (var i = 0; i < length; i++) obj[keys[i]] = this[i];
		return obj;
	},

	/*
	Property: extend
		Extends an array with another one.

	Arguments:
		array - the array to extend ours with

	Example:
		>var Animals = ['Cat', 'Dog', 'Coala'];
		>Animals.extend(['Lizard']);
		>//Animals is now: ['Cat', 'Dog', 'Coala', 'Lizard'];
	*/

	extend: function(array){
		for (var i = 0, j = array.length; i < j; i++) this.push(array[i]);
		return this;
	},

	/*
	Property: merge
		merges an array in another array, without duplicates. (case- and type-sensitive)

	Arguments:
		array - the array to merge from.

	Example:
		>['Cat','Dog'].merge(['Dog','Coala']); //returns ['Cat','Dog','Coala']
	*/

	merge: function(array){
		for (var i = 0, l = array.length; i < l; i++) this.include(array[i]);
		return this;
	},

	/*
	Property: include
		includes the passed in element in the array, only if its not already present. (case- and type-sensitive)

	Arguments:
		item - item to add to the array (if not present)

	Example:
		>['Cat','Dog'].include('Dog'); //returns ['Cat','Dog']
		>['Cat','Dog'].include('Coala'); //returns ['Cat','Dog','Coala']
	*/

	include: function(item){
		if (!this.length || !this.contains(item)) this.push(item);
		return this;
	},

	/*
	Property: getRandom
		returns a random item in the Array
	*/

	getRandom: function(){
		return this[Moo.$random(0, this.length - 1)];
	},

	/*
	Property: getLast
		returns the last item in the Array
	*/

	getLast: function(){
		return this[this.length - 1];
	}

});

//copies

Array.prototype.each = Array.prototype.forEach;
Array.prototype.test = Array.prototype.contains;
Array.prototype.removeItem = Array.prototype.remove;

/* Section: Utility Functions */

/*
Function: Moo.$A()
	Same as <Array.copy>, but as function.
	Useful to apply Array prototypes to iterable objects, as a collection of DOM elements or the arguments object.

Example:
	(start code)
	function myFunction(){
		Moo.$A(arguments).each(argument, function(){
			alert(argument);
		});
	};
	//the above will alert all the arguments passed to the function myFunction.
	(end)
*/

Moo.$A = function(array, start, length){
	return Array.prototype.copy.call(array, start, length);
};

/*
Function: Moo.$each
	Use to iterate through iterables that are not regular arrays, such as builtin getElementsByTagName calls, arguments of a function, or an object.

Arguments:
	iterable - an iterable element or an objct.
	function - function to apply to the iterable.
	bind - optional, the 'this' of the function will refer to this object.

Function argument:
	The function argument will be passed the following arguments.

	item - the current item in the iterator being procesed
	index - integer; the index of the item, or key in case of an object.

Examples:
	(start code)
	Moo.$each(['Sun','Mon','Tue'], function(day, index) {
		alert('name:' + day + ', index: ' + index);
	});
	//alerts "name: Sun, index: 0", "name: Mon, index: 1", etc.
	//over an object
	Moo.$each({first: "Sunday", second: "Monday", third: "Tuesday"}, function(value, key){
		alert("the " + key + " day of the week is " + value);
	});
	//alerts "the first day of the week is Sunday",
	//"the second day of the week is Monday", etc.
	(end)
*/

Moo.$each = function(iterable, fn, bind){
	if (iterable.length != undefined) Array.prototype.forEach.call(iterable, fn, bind);
	else for (var name in iterable) fn.call(bind || iterable, iterable[name], name);
};

/*
Script: String.js
	Contains String prototypes and Number prototypes.

License:
	MIT-style license.
*/

/*
Moo.Class: String
	A collection of The String Object prototype methods.
*/

String.extend({

	/*
	Property: test
		Tests a string with a regular expression.

	Arguments:
		regex - a string or regular expression object, the regular expression you want to match the string with
		params - optional, if first parameter is a string, any parameters you want to pass to the regex ('g' has no effect)

	Returns:
		true if a match for the regular expression is found in the string, false if not.
		See <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Objects:RegExp:test>

	Example:
		>"I like cookies".test("cookie"); // returns true
		>"I like cookies".test("COOKIE", "i") // ignore case, returns true
		>"I like cookies".test("cake"); // returns false
	*/

	test: function(regex, params){
		return ((typeof regex == 'string') ? new RegExp(regex, params) : regex).test(this);
	},

	/*
	Property: toInt
		parses a string to an integer.

	Returns:
		either an int or "NaN" if the string is not a number.

	Example:
		>var value = "10px".toInt(); // value is 10
	*/

	toInt: function(){
		return parseInt(this, 10);
	},

	/*
	Property: toFloat
		parses a string to an float.

	Returns:
		either a float or "NaN" if the string is not a number.

	Example:
		>var value = "10.848".toFloat(); // value is 10.848
	*/

	toFloat: function(){
		return parseFloat(this);
	},

	/*
	Property: camelCase
		Converts a hiphenated string to a camelcase string.

	Example:
		>"I-like-cookies".camelCase(); //"ILikeCookies"

	Returns:
		the camel cased string
	*/

	camelCase: function(){
		return this.replace(/-\D/g, function(match){
			return match.charAt(1).toUpperCase();
		});
	},

	/*
	Property: hyphenate
		Converts a camelCased string to a hyphen-ated string.

	Example:
		>"ILikeCookies".hyphenate(); //"I-like-cookies"
	*/

	hyphenate: function(){
		return this.replace(/\w[A-Z]/g, function(match){
			return (match.charAt(0) + '-' + match.charAt(1).toLowerCase());
		});
	},

	/*
	Property: capitalize
		Converts the first letter in each word of a string to Uppercase.

	Example:
		>"i like cookies".capitalize(); //"I Like Cookies"

	Returns:
		the capitalized string
	*/

	capitalize: function(){
		return this.replace(/\b[a-z]/g, function(match){
			return match.toUpperCase();
		});
	},

	/*
	Property: trim
		Trims the leading and trailing spaces off a string.

	Example:
		>"    i like cookies     ".trim() //"i like cookies"

	Returns:
		the trimmed string
	*/

	trim: function(){
		return this.replace(/^\s+|\s+$/g, '');
	},

	/*
	Property: clean
		trims (<String.trim>) a string AND removes all the double spaces in a string.

	Returns:
		the cleaned string

	Example:
		>" i      like     cookies      \n\n".clean() //"i like cookies"
	*/

	clean: function(){
		return this.replace(/\s{2,}/g, ' ').trim();
	},

	/*
	Property: rgbToHex
		Converts an RGB value to hexidecimal. The string must be in the format of "rgb(255,255,255)" or "rgba(255,255,255,1)";

	Arguments:
		array - boolean value, defaults to false. Use true if you want the array ['FF','33','00'] as output instead of "#FF3300"

	Returns:
		hex string or array. returns "transparent" if the output is set as string and the fourth value of rgba in input string is 0.

	Example:
		>"rgb(17,34,51)".rgbToHex(); //"#112233"
		>"rgba(17,34,51,0)".rgbToHex(); //"transparent"
		>"rgb(17,34,51)".rgbToHex(true); //['11','22','33']
	*/

	rgbToHex: function(array){
		var rgb = this.match(/\d{1,3}/g);
		return (rgb) ? rgb.rgbToHex(array) : false;
	},

	/*
	Property: hexToRgb
		Converts a hexidecimal color value to RGB. Input string must be the hex color value (with or without the hash). Also accepts triplets ('333');

	Arguments:
		array - boolean value, defaults to false. Use true if you want the array [255,255,255] as output instead of "rgb(255,255,255)";

	Returns:
		rgb string or array.

	Example:
		>"#112233".hexToRgb(); //"rgb(17,34,51)"
		>"#112233".hexToRgb(true); //[17,34,51]
	*/

	hexToRgb: function(array){
		var hex = this.match(/^#?(\w{1,2})(\w{1,2})(\w{1,2})$/);
		return (hex) ? hex.slice(1).hexToRgb(array) : false;
	},

	/*
	Property: contains
		checks if the passed in string is contained in the String. also accepts an optional second parameter, to check if the string is contained in a list of separated values.

	Example:
		>'a b c'.contains('c', ' '); //true
		>'a bc'.contains('bc'); //true
		>'a bc'.contains('b', ' '); //false
	*/
	
	contains: function(string, s){
		return (s) ? (s + this + s).indexOf(s + string + s) > -1 : this.indexOf(string) > -1;
	},

	/*
	Property: escapeRegExp
		Returns string with escaped regular expression characters

	Example:
		>var search = 'animals.sheeps[1]'.escapeRegExp(); // search is now 'animals\.sheeps\[1\]'

	Returns:
		Escaped string
	*/

	escapeRegExp: function(){
		return this.replace(/([.*+?^${}()|[\]\/\\])/g, '\\Moo.$1');
	}

});

Array.extend({

	/*
	Property: rgbToHex
		see <String.rgbToHex>, but as an array method.
	*/

	rgbToHex: function(array){
		if (this.length < 3) return false;
		if (this[3] && (this[3] == 0) && !array) return 'transparent';
		var hex = [];
		for (var i = 0; i < 3; i++){
			var bit = (this[i] - 0).toString(16);
			hex.push((bit.length == 1) ? '0' + bit : bit);
		}
		return array ? hex : '#' + hex.join('');
	},

	/*
	Property: hexToRgb
		same as <String.hexToRgb>, but as an array method.
	*/

	hexToRgb: function(array){
		if (this.length != 3) return false;
		var rgb = [];
		for (var i = 0; i < 3; i++){
			rgb.push(parseInt((this[i].length == 1) ? this[i] + this[i] : this[i], 16));
		}
		return array ? rgb : 'rgb(' + rgb.join(',') + ')';
	}

});

/*
Moo.Class: Number
	contains the internal method toInt.
*/

Number.extend({

	/*
	Property: toInt
		Returns this number; useful because toInt must work on both Strings and Numbers.
	*/

	toInt: function(){
		return parseInt(this);
	},

	/*
	Property: toFloat
		Returns this number as a float; useful because toFloat must work on both Strings and Numbers.
	*/

	toFloat: function(){
		return parseFloat(this);
	}

});

/* 
Script: Function.js
	Contains Function prototypes and utility functions .

License:
	MIT-style license.

Credits:
	- Some functions are inspired by those found in prototype.js <http://prototype.conio.net/> (c) 2005 Sam Stephenson sam [at] conio [dot] net, MIT-style license
*/

/*
Moo.Class: Function
	A collection of The Function Object prototype methods.
*/

Function.extend({

	/*
	Property: create
		Main function to create closures.

	Returns:
		a function.

	Arguments:
		options - An Moo.Options object.

	Moo.Options:
		bind - The object that the "this" of the function will refer to. Default is the current function.
		event - If set to true, the function will act as an event listener and receive an event as first argument.
				If set to a class name, the function will receive a new instance of this class (with the event passed as argument's constructor) as first argument.
				Default is false.
		arguments - A single argument or array of arguments that will be passed to the function when called.
		
					If both the event and arguments options are set, the event is passed as first argument and the arguments array will follow.
					
					Default is no custom arguments, the function will receive the standard arguments when called.
					
		delay - Numeric value: if set, the returned function will delay the actual execution by this amount of milliseconds and return a timer handle when called.
				Default is no delay.
		periodical - Numeric value: if set, the returned function will periodically perform the actual execution with this specified interval and return a timer handle when called.
				Default is no periodical execution.
		attempt - If set to true, the returned function will try to execute and return either the results or the error when called. Default is false.
	*/

	create: function(options){
		var fn = this;
		options = Moo.$merge({
			'bind': fn,
			'event': false,
			'arguments': null,
			'delay': false,
			'periodical': false,
			'attempt': false
		}, options);
		if (Moo.$chk(options.arguments) && Moo.$type(options.arguments) != 'array') options.arguments = [options.arguments];
		return function(event){
			var args;
			if (options.event){
				event = event || window.event;
				args = [(options.event === true) ? event : new options.event(event)];
				if (options.arguments) args = args.concat(options.arguments);
			}
			else args = options.arguments || arguments;
			var returns = function(){
				return fn.apply(Moo.$pick(options.bind, fn), args);
			};
			if (options.delay) return setTimeout(returns, options.delay);
			if (options.periodical) return setInterval(returns, options.periodical);
			if (options.attempt) try {return returns();} catch(err){return false;};
			return returns();
		};
	},

	/*
	Property: pass
		Shortcut to create closures with arguments and bind.

	Returns:
		a function.

	Arguments:
		args - the arguments passed. must be an array if arguments > 1
		bind - optional, the object that the "this" of the function will refer to.

	Example:
		>myFunction.pass([arg1, arg2], myElement);
	*/

	pass: function(args, bind){
		return this.create({'arguments': args, 'bind': bind});
	},

	/*
	Property: attempt
		Tries to execute the function, returns either the function results or the error.

	Arguments:
		args - the arguments passed. must be an array if arguments > 1
		bind - optional, the object that the "this" of the function will refer to.

	Example:
		>myFunction.attempt([arg1, arg2], myElement);
	*/

	attempt: function(args, bind){
		return this.create({'arguments': args, 'bind': bind, 'attempt': true})();
	},

	/*
	Property: bind
		method to easily create closures with "this" altered.

	Arguments:
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, the arguments passed. must be an array if arguments > 1

	Returns:
		a function.

	Example:
		>function myFunction(){
		>	this.setStyle('color', 'red');
		>	// note that 'this' here refers to myFunction, not an element
		>	// we'll need to bind this function to the element we want to alter
		>};
		>var myBoundFunction = myFunction.bind(myElement);
		>myBoundFunction(); // this will make the element myElement red.
	*/

	bind: function(bind, args){
		return this.create({'bind': bind, 'arguments': args});
	},

	/*
	Property: bindAsEventListener
		cross browser method to pass event firer

	Arguments:
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, the arguments passed. must be an array if arguments > 1

	Returns:
		a function with the parameter bind as its "this" and as a pre-passed argument event or window.event, depending on the browser.

	Example:
		>function myFunction(event){
		>	alert(event.clientx) //returns the coordinates of the mouse..
		>};
		>myElement.onclick = myFunction.bindAsEventListener(myElement);
	*/

	bindAsEventListener: function(bind, args){
		return this.create({'bind': bind, 'event': true, 'arguments': args});
	},

	/*
	Property: delay
		Delays the execution of a function by a specified duration.

	Arguments:
		delay - the duration to wait in milliseconds.
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, the arguments passed. must be an array if arguments > 1

	Example:
		>myFunction.delay(50, myElement) //wait 50 milliseconds, then call myFunction and bind myElement to it
		>(function(){alert('one second later...')}).delay(1000); //wait a second and alert
	*/

	delay: function(delay, bind, args){
		return this.create({'delay': delay, 'bind': bind, 'arguments': args})();
	},

	/*
	Property: periodical
		Executes a function in the specified intervals of time

	Arguments:
		interval - the duration of the intervals between executions.
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, the arguments passed. must be an array if arguments > 1
	*/

	periodical: function(interval, bind, args){
		return this.create({'periodical': interval, 'bind': bind, 'arguments': args})();
	}

});

/*
Script: Moo.Element.js
	Contains useful Moo.Element prototypes, to be used with the dollar function <Moo.$>.

License:
	MIT-style license.

Credits:
	- Some functions are inspired by those found in prototype.js <http://prototype.conio.net/> (c) 2005 Sam Stephenson sam [at] conio [dot] net, MIT-style license
*/

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element = new Moo.Class({

	/*
	Property: initialize
		Creates a new element of the type passed in.

	Arguments:
		el - string; the tag name for the element you wish to create. you can also pass in an element reference, in which case it will be extended.
		props - object; the properties you want to add to your element.
		Accepts the same keys as <Moo.Element.setProperties>, but also allows events and styles

	Props:
		the key styles will be used as setStyles, the key events will be used as addEvents. any other key is used as setProperty.

	Example:
		(start code)
		new Moo.Element('a', {
			'styles': {
				'display': 'block',
				'border': '1px solid black'
			},
			'events': {
				'click': function(){
					//aaa
				},
				'mousedown': function(){
					//aaa
				}
			},
			'class': 'myClassSuperClass',
			'href': 'http://mad4milk.net'
		});

		(end)
	*/

	initialize: function(el, props){
		if (Moo.$type(el) == 'string'){
			if (window.ie && props && (props.name || props.type)){
				var name = (props.name) ? ' name="' + props.name + '"' : '';
				var type = (props.type) ? ' type="' + props.type + '"' : '';
				delete props.name;
				delete props.type;
				el = '<' + el + name + type + '>';
			}
			el = document.createElement(el);
		}
		el = Moo.$(el);
		if (!props || !el) return el;
		for (var prop in props){
			var val = props[prop];
			switch(prop){
				case 'styles': el.setStyles(val); break;
				case 'events': if (el.addEvents) el.addEvents(val); break;
				case 'properties': el.setProperties(val); break;
				default: el.setProperty(prop, val);
			}
		}
		return el;
	}

});

/*
Moo.Class: Moo.Elements
	- Every dom function such as <Moo.$$>, or in general every function that returns a collection of nodes in mootools, returns them as an Moo.Elements class.
	- The purpose of the Moo.Elements class is to allow <Moo.Element> methods to work also on <Moo.Elements> array.
	- Moo.Elements is also an Array, so it accepts all the <Array> methods.
	- Every node of the Moo.Elements instance is already extended with <Moo.$>.

Example:
	>Moo.$$('myselector').each(function(el){
	> //...
	>});
	
	some iterations here, Moo.$$('myselector') is also an array.
	
	>Moo.$$('myselector').setStyle('color', 'red');
	every element returned by Moo.$$('myselector') also accepts <Moo.Element> methods, in this example every element will be made red.
*/

Moo.Elements = new Moo.Class({});

Moo.Elements.extend = Moo.Class.prototype.implement;

/*
Section: Utility Functions

Function: Moo.$
	returns the element passed in with all the Moo.Element prototypes applied.

Arguments:
	el - a reference to an actual element or a string representing the id of an element

Example:
	>Moo.$('myElement') // gets a DOM element by id with all the Moo.Element prototypes applied.
	>var div = document.getElementById('myElement');
	>Moo.$(div) //returns an Moo.Element also with all the mootools extentions applied.

	You'll use this when you aren't sure if a variable is an actual element or an id, as
	well as just shorthand for document.getElementById().

Returns:
	a DOM element or false (if no id was found).

Note:
	you need to call Moo.$ on an element only once to get all the prototypes.
	But its no harm to call it multiple times, as it will detect if it has been already extended.
*/

Moo.$ = function(el){
	if (!el) return false;
	if (el.htmlElement) return Moo.Garbage.collect(el);
	if ([window, document].contains(el)) return el;
	var type = Moo.$type(el);
	if (type == 'string'){
		el = document.getElementById(el);
		type = (el) ? 'element' : false;
	}
	if (type != 'element') return false;
	if (el.htmlElement) return Moo.Garbage.collect(el);
	if (['object', 'embed'].contains(el.tagName.toLowerCase())) return el;
	Moo.$extend(el, Moo.Element.prototype);
	el.htmlElement = true;
	return Moo.Garbage.collect(el);
};

/*
Function: Moo.$$
	Selects, and extends DOM elements. Moo.Elements arrays returned with Moo.$$ will also accept all the <Moo.Element> methods.
	The return type of element methods run through Moo.$$ is always an array. If the return array is only made by elements,
	Moo.$$ will be applied automatically.

Arguments:
	HTML Collections, arrays of elements, arrays of strings as element ids, elements, strings as selectors.
	Any number of the above as arguments are accepted.

Note:
	if you load <Dom.js>, Moo.$$ will also accept CSS Selectors, otherwise the only selectors supported are tag names.

Example:
	>Moo.$$('a') //an array of all anchor tags on the page
	>Moo.$$('a', 'b') //an array of all anchor and bold tags on the page
	>Moo.$$('#myElement') //array containing only the element with id = myElement. (only with <Dom.js>)
	>Moo.$$('#myElement a.myClass') //an array of all anchor tags with the class "myClass"
	>//within the DOM element with id "myElement" (only with <Dom.js>)
	>Moo.$$(myelement, myelement2, 'a', ['myid', myid2, 'myid3'], document.getElementsByTagName('div')) //an array containing:
	>// the element referenced as myelement if existing,
	>// the element referenced as myelement2 if existing,
	>// all the elements with a as tag in the page,
	>// the element with id = myid if existing
	>// the element with id = myid2 if existing
	>// the element with id = myid3 if existing
	>// all the elements with div as tag in the page

Returns:
	array - array of all the dom elements matched, extended with <Moo.$>.  Returns as <Moo.Elements>.
*/

document.getElementsBySelector = document.getElementsByTagName;

Moo.$$ = function(){
	if (!arguments) return false;
	var elements = [];
	for (var i = 0, j = arguments.length; i < j; i++){
		var selector = arguments[i];
		switch(Moo.$type(selector)){
			case 'element': elements.push(selector);
			case 'boolean':
			case false: break;
			case 'string': selector = document.getElementsBySelector(selector, true);
			default: elements = elements.concat((selector.push) ? selector : Moo.$A(selector));
		}
	}
	return Moo.$$.unique(elements);
};

Moo.$$.unique = function(array){
	var elements = [];
	for (var i = 0, l = array.length; i < l; i++){
		if (array[i].$included) continue;
		var element = Moo.$(array[i]);
		if (element && !element.$included){
			element.$included = true;
			elements.push(element);
		}
	}
	for (var i = 0, l = elements.length; i < l; i++) elements[i].$included = null;
	return Moo.$extend(elements, new Moo.Elements);
};

Moo.Elements.Multi = function(property){
	return function(){
		var args = arguments;
		var items = [];
		var elements = true;
		for (var i = 0, j = this.length, returns; i < j; i++){
			returns = this[i][property].apply(this[i], args);
			if (Moo.$type(returns) != 'element') elements = false;
			items.push(returns);
		};
		return (elements) ? Moo.$$.unique(items) : items;
	};
};

Moo.Element.extend = function(properties){
	for (var property in properties){
		HTMLElement.prototype[property] = properties[property];
		Moo.Element.prototype[property] = properties[property];
		Moo.Elements.prototype[property] = Moo.Elements.Multi(property);
	}
};

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.extend({

	inject: function(el, where){
		el = Moo.$(el);
		switch(where){
			case 'before': el.parentNode.insertBefore(this, el); break;
			case 'after':
				var next = el.getNext();
				if (!next) el.parentNode.appendChild(this);
				else el.parentNode.insertBefore(this, next);
				break;
			case 'top':
				var first = el.firstChild;
				if (first){
					el.insertBefore(this, first);
					break;
				}
			default: el.appendChild(this);
		}
		return this;
	},

	/*
	Property: injectBefore
		Inserts the Moo.Element before the passed element.

	Parameteres:
		el - an element reference or the id of the element to be injected in.

	Example:
		>html:
		><div id="myElement"></div>
		><div id="mySecondElement"></div>
		>js:
		>Moo.$('mySecondElement').injectBefore('myElement');
		>resulting html:
		><div id="mySecondElement"></div>
		><div id="myElement"></div>
	*/

	injectBefore: function(el){
		return this.inject(el, 'before');
	},

	/*
	Property: injectAfter
		Same as <Moo.Element.injectBefore>, but inserts the element after.
	*/

	injectAfter: function(el){
		return this.inject(el, 'after');
	},

	/*
	Property: injectInside
		Same as <Moo.Element.injectBefore>, but inserts the element inside.
	*/

	injectInside: function(el){
		return this.inject(el, 'bottom');
	},
	
	/*
	Property: injectTop
		Same as <Moo.Element.injectInside>, but inserts the element inside, at the top.
	*/
	
	injectTop: function(el){
		return this.inject(el, 'top');
	},

	/*
	Property: adopt
		Inserts the passed element(s) inside the Moo.Element. Works as <Moo.Element.injectInside> but in reverse and with any number of elements.

	Parameteres:
		el - an element reference or the id of the element to be injected in.
	*/

	adopt: function(){
		Moo.$$.unique(arguments).injectInside(this);
		return this;
	},

	/*
	Property: remove
		Removes the Moo.Element from the DOM.

	Example:
		>Moo.$('myElement').remove() //bye bye
	*/

	remove: function(){
		return this.parentNode.removeChild(this);
	},

	/*
	Property: clone
		Clones the Moo.Element and returns the cloned one.

	Returns:
		the cloned element

	Example:
		>var clone = Moo.$('myElement').clone().injectAfter('myElement');
		>//clones the Moo.Element and append the clone after the Moo.Element.
	*/

	clone: function(contents){
		return Moo.$(this.cloneNode(contents !== false));
	},

	/*
	Property: replaceWith
		Replaces the Moo.Element with an element passed.

	Parameteres:
		el - a string representing the element to be injected in (myElementId, or div), or an element reference.
		If you pass div or another tag, the element will be created.

	Returns:
		the passed in element

	Example:
		>Moo.$('myOldElement').replaceWith(Moo.$('myNewElement')); //Moo.$('myOldElement') is gone, and Moo.$('myNewElement') is in its place.
	*/

	replaceWith: function(el){
		el = Moo.$(el);
		this.parentNode.replaceChild(el, this);
		return el;
	},

	/*
	Property: appendText
		Appends text node to a DOM element.

	Arguments:
		text - the text to append.

	Example:
		><div id="myElement">hey</div>
		>Moo.$('myElement').appendText(' howdy'); //myElement innerHTML is now "hey howdy"
	*/

	appendText: function(text){
		if (window.ie){
			switch(this.getTag()){
				case 'style': this.styleSheet.cssText = text; return this;
				case 'script': return this.setProperty('text', text);
			}
		}
		this.appendChild(document.createTextNode(text));
		return this;
	},

	/*
	Property: hasClass
		Tests the Moo.Element to see if it has the passed in className.

	Returns:
		true - the Moo.Element has the class
		false - it doesn't

	Arguments:
		className - string; the class name to test.

	Example:
		><div id="myElement" class="testClass"></div>
		>Moo.$('myElement').hasClass('testClass'); //returns true
	*/

	hasClass: function(className){
		return this.className.contains(className, ' ');
	},

	/*
	Property: addClass
		Adds the passed in class to the Moo.Element, if the element doesnt already have it.

	Arguments:
		className - string; the class name to add

	Example:
		><div id="myElement" class="testClass"></div>
		>Moo.$('myElement').addClass('newClass'); //<div id="myElement" class="testClass newClass"></div>
	*/

	addClass: function(className){
		if (!this.hasClass(className)) this.className = (this.className + ' ' + className).clean();
		return this;
	},

	/*
	Property: removeClass
		Works like <Moo.Element.addClass>, but removes the class from the element.
	*/

	removeClass: function(className){
		this.className = this.className.replace(new RegExp('(^|\\s)' + className + '(?:\\s|Moo.$)'), 'Moo.$1').clean();
		return this;
	},

	/*
	Property: toggleClass
		Adds or removes the passed in class name to the element, depending on if it's present or not.

	Arguments:
		className - the class to add or remove

	Example:
		><div id="myElement" class="myClass"></div>
		>Moo.$('myElement').toggleClass('myClass');
		><div id="myElement" class=""></div>
		>Moo.$('myElement').toggleClass('myClass');
		><div id="myElement" class="myClass"></div>
	*/

	toggleClass: function(className){
		return this.hasClass(className) ? this.removeClass(className) : this.addClass(className);
	},

	/*
	Property: setStyle
		Sets a css property to the Moo.Element.

		Arguments:
			property - the property to set
			value - the value to which to set it; for numeric values that require "px" you can pass an integer

		Example:
			>Moo.$('myElement').setStyle('width', '300px'); //the width is now 300px
			>Moo.$('myElement').setStyle('width', 300); //the width is now 300px
	*/

	setStyle: function(property, value){
		switch(property){
			case 'opacity': return this.setOpacity(parseFloat(value));
			case 'float': property = (window.ie) ? 'styleFloat' : 'cssFloat';
		}
		property = property.camelCase();
		switch(Moo.$type(value)){
			case 'number': if (!['zIndex', 'zoom'].contains(property)) value += 'px'; break;
			case 'array': value = 'rgb(' + value.join(',') + ')';
		}
		this.style[property] = value;
		return this;
	},

	/*
	Property: setStyles
		Applies a collection of styles to the Moo.Element.

	Arguments:
		source - an object or string containing all the styles to apply. When its a string it overrides old style.

	Examples:
		>Moo.$('myElement').setStyles({
		>	border: '1px solid #000',
		>	width: 300,
		>	height: 400
		>});

		OR

		>Moo.$('myElement').setStyles('border: 1px solid #000; width: 300px; height: 400px;');
	*/

	setStyles: function(source){
		switch(Moo.$type(source)){
			case 'object': Moo.Element.setMany(this, 'setStyle', source); break;
			case 'string': this.style.cssText = source;
		}
		return this;
	},

	/*
	Property: setOpacity
		Sets the opacity of the Moo.Element, and sets also visibility == "hidden" if opacity == 0, and visibility = "visible" if opacity > 0.

	Arguments:
		opacity - float; Accepts values from 0 to 1.

	Example:
		>Moo.$('myElement').setOpacity(0.5) //make it 50% transparent
	*/

	setOpacity: function(opacity){
		if (opacity == 0){
			if (this.style.visibility != "hidden") this.style.visibility = "hidden";
		} else {
			if (this.style.visibility != "visible") this.style.visibility = "visible";
		}
		if (!this.currentStyle || !this.currentStyle.hasLayout) this.style.zoom = 1;
		if (window.ie) this.style.filter = (opacity == 1) ? '' : "alpha(opacity=" + opacity * 100 + ")";
		this.style.opacity = this.$.opacity = opacity;
		return this;
	},

	/*
	Property: getStyle
		Returns the style of the Moo.Element given the property passed in.

	Arguments:
		property - the css style property you want to retrieve

	Example:
		>Moo.$('myElement').getStyle('width'); //returns "400px"
		>//but you can also use
		>Moo.$('myElement').getStyle('width').toInt(); //returns 400

	Returns:
		the style as a string
	*/

	getStyle: function(property){
		property = property.camelCase();
		var result = this.style[property];
		if (!Moo.$chk(result)){
			if (property == 'opacity') return this.$.opacity;
			var result = [];
			for (var style in Moo.Element.Styles){
				if (property == style){
					Moo.Element.Styles[style].each(function(s){
						result.push(this.getStyle(s));
					}, this);
					if (property == 'border'){
						var every = result.every(function(bit){
							return (bit == result[0]);
						});
						return (every) ? result[0] : false;
					}
					return result.join(' ');
				}
			}
			if (Moo.Element.Styles.border.contains(property)){
				['Width', 'Moo.Color', 'Style'].each(function(p){
					result.push(this.getStyle(property + p));
				}, this);
				return result.join(' ');
			}
			if (document.defaultView) result = document.defaultView.getComputedStyle(this, null).getPropertyValue(property.hyphenate());
			else if (this.currentStyle) result = this.currentStyle[property];
		}
		if (window.ie) result = Moo.Element.fixStyle(property, result, this);
		return (result && property.test(/color/i) && result.contains('rgb')) ? result.rgbToHex() : result;
	},

	/*
	Property: getStyles
		Returns an object of styles of the Moo.Element for each argument passed in.
		Arguments:
		properties - strings; any number of style properties
	Example:
		>Moo.$('myElement').getStyles('width','height','padding');
		>//returns an object like:
		>{width: "10px", height: "10px", padding: "10px 0px 10px 0px"}
	*/

	getStyles: function(){
		return Moo.Element.getMany(this, 'getStyle', arguments);
	},

	walk: function(brother, start){
		brother += 'Sibling';
		var el = (start) ? this[start] : this[brother];
		while (el && Moo.$type(el) != 'element') el = el[brother];
		return Moo.$(el);
	},

	/*
	Property: getPrevious
		Returns the previousSibling of the Moo.Element, excluding text nodes.

	Example:
		>Moo.$('myElement').getPrevious(); //get the previous DOM element from myElement

	Returns:
		the sibling element or undefined if none found.
	*/

	getPrevious: function(){
		return this.walk('previous');
	},

	/*
	Property: getNext
		Works as Moo.Element.getPrevious, but tries to find the nextSibling.
	*/

	getNext: function(){
		return this.walk('next');
	},

	/*
	Property: getFirst
		Works as <Moo.Element.getPrevious>, but tries to find the firstChild.
	*/

	getFirst: function(){
		return this.walk('next', 'firstChild');
	},

	/*
	Property: getLast
		Works as <Moo.Element.getPrevious>, but tries to find the lastChild.
	*/

	getLast: function(){
		return this.walk('previous', 'lastChild');
	},

	/*
	Property: getParent
		returns the Moo.$(element.parentNode)
	*/

	getParent: function(){
		return Moo.$(this.parentNode);
	},

	/*
	Property: getChildren
		returns all the Moo.$(element.childNodes), excluding text nodes. Returns as <Moo.Elements>.
	*/

	getChildren: function(){
		return Moo.$$(this.childNodes);
	},

	/*
	Property: hasChild
		returns true if the passed in element is a child of the Moo.$(element).
	*/

	hasChild: function(el) {
		return !!Moo.$A(this.getElementsByTagName('*')).contains(el);
	},

	/*
	Property: getProperty
		Gets the an attribute of the Moo.Element.

	Arguments:
		property - string; the attribute to retrieve

	Example:
		>Moo.$('myImage').getProperty('src') // returns whatever.gif

	Returns:
		the value, or an empty string
	*/

	getProperty: function(property){
		var index = Moo.Element.Properties[property];
		return (index) ? this[index] : this.getAttribute(property);
	},

	/*
	Property: removeProperty
		Removes an attribute from the Moo.Element

	Arguments:
		property - string; the attribute to remove
	*/

	removeProperty: function(property){
		var index = Moo.Element.Properties[property];
		if (index) this[index] = '';
		else this.removeAttribute(property);
		return this;
	},

	/*
	Property: getProperties
		same as <Moo.Element.getStyles>, but for properties
	*/

	getProperties: function(){
		return Moo.Element.getMany(this, 'getProperty', arguments);
	},

	/*
	Property: setProperty
		Sets an attribute for the Moo.Element.

	Arguments:
		property - string; the property to assign the value passed in
		value - the value to assign to the property passed in

	Example:
		>Moo.$('myImage').setProperty('src', 'whatever.gif'); //myImage now points to whatever.gif for its source
	*/

	setProperty: function(property, value){
		var index = Moo.Element.Properties[property];
		if (index) this[index] = value;
		else this.setAttribute(property, value);
		return this;
	},

	/*
	Property: setProperties
		Sets numerous attributes for the Moo.Element.

	Arguments:
		source - an object with key/value pairs.

	Example:
		(start code)
		Moo.$('myElement').setProperties({
			src: 'whatever.gif',
			alt: 'whatever dude'
		});
		<img src="whatever.gif" alt="whatever dude">
		(end)
	*/

	setProperties: function(source){
		return Moo.Element.setMany(this, 'setProperty', source);
	},

	/*
	Property: setHTML
		Sets the innerHTML of the Moo.Element.

	Arguments:
		html - string; the new innerHTML for the element.

	Example:
		>Moo.$('myElement').setHTML(newHTML) //the innerHTML of myElement is now = newHTML
	*/

	setHTML: function(){
		this.innerHTML = Moo.$A(arguments).join('');
		return this;
	},

	/*
	Property: getTag
		Returns the tagName of the element in lower case.

	Example:
		>Moo.$('myImage').getTag() // returns 'img'

	Returns:
		The tag name in lower case
	*/

	getTag: function(){
		return this.tagName.toLowerCase();
	},

	/*
	Property: empty
		Empties an element of all its children.

	Example:
		>Moo.$('myDiv').empty() // empties the Div and returns it

	Returns:
		The element.
	*/

	empty: function(){
		Moo.Garbage.trash(this.getElementsByTagName('*'));
		return this.setHTML('');
	}

});

Moo.Element.fixStyle = function(property, result, element){
	if (Moo.$chk(parseInt(result))) return result;
	if (['height', 'width'].contains(property)){
		var values = (property == 'width') ? ['left', 'right'] : ['top', 'bottom'];
		var size = 0;
		values.each(function(value){
			size += element.getStyle('border-' + value + '-width').toInt() + element.getStyle('padding-' + value).toInt();
		});
		return element['offset' + property.capitalize()] - size + 'px';
	} else if (property.test(/border(.+)Width/)){
		return '0px';
	}
	return result;
};

Moo.Element.Styles = {'border': [], 'padding': [], 'margin': []};
['Top', 'Right', 'Bottom', 'Left'].each(function(direction){
	for (var style in Moo.Element.Styles) Moo.Element.Styles[style].push(style + direction);
});

Moo.Element.getMany = function(el, method, keys){
	var result = {};
	Moo.$each(keys, function(key){
		result[key] = el[method](key);
	});
	return result;
};

Moo.Element.setMany = function(el, method, pairs){
	for (var key in pairs) el[method](key, pairs[key]);
	return el;
};

Moo.Element.Properties = new Moo.Abstract({
	'class': 'className', 'for': 'htmlFor', 'colspan': 'colSpan',
	'rowspan': 'rowSpan', 'accesskey': 'accessKey', 'tabindex': 'tabIndex',
	'maxlength': 'maxLength', 'readonly': 'readOnly', 'value': 'value',
	'disabled': 'disabled', 'checked': 'checked', 'multiple': 'multiple'
});

Moo.Element.listenerMethods = {

	addListener: function(type, fn){
		if (this.addEventListener) this.addEventListener(type, fn, false);
		else this.attachEvent('on' + type, fn);
		return this;
	},

	removeListener: function(type, fn){
		if (this.removeEventListener) this.removeEventListener(type, fn, false);
		else this.detachEvent('on' + type, fn);
		return this;
	}

};

window.extend(Moo.Element.listenerMethods);
document.extend(Moo.Element.listenerMethods);
Moo.Element.extend(Moo.Element.listenerMethods);

Moo.Element.Events = new Moo.Abstract({});

Moo.Garbage = {

	elements: [],

	collect: function(el){
		if (!el.$){
			Moo.Garbage.elements.push(el);
			el.$ = {'opacity': 1};
		}
		return el;
	},

	trash: function(elements){
		for (var i = 0, j = elements.length, el; i < j; i++){
			if (!(el = elements[i]) || !el.$) return;
			if (el.$events) {
				el.fireEvent('onTrash');
				el.removeEvents();
			}
			for (var p in el.$) el.$[p] = null;
			for (var p in Moo.Element.prototype) el[p] = null;
			el.htmlElement = el.$ = null;
			Moo.Garbage.elements.remove(el);
		}
	},

	empty: function(){
		Moo.Garbage.collect(window);
		Moo.Garbage.collect(document);
		Moo.Garbage.trash(Moo.Garbage.elements);
	}

};

window.addListener('unload', Moo.Garbage.empty);

/*
Script: Moo.Event.js
	Moo.Event class

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Event
	Cross browser methods to manage events.

Arguments:
	event - the event

Properties:
	shift - true if the user pressed the shift
	control - true if the user pressed the control 
	alt - true if the user pressed the alt
	meta - true if the user pressed the meta key
	wheel - the amount of third button scrolling
	code - the keycode of the key pressed
	page.x - the x position of the mouse, relative to the full window
	page.y - the y position of the mouse, relative to the full window
	client.x - the x position of the mouse, relative to the viewport
	client.y - the y position of the mouse, relative to the viewport
	key - the key pressed as a lowercase string. key also returns 'enter', 'up', 'down', 'left', 'right', 'space', 'backspace', 'delete', 'esc'. Handy for these special keys.
	target - the event target
	relatedTarget - the event related target

Example:
	(start code)
	Moo.$('myLink').onkeydown = function(event){
		var event = new Moo.Event(event);
		//event is now the Moo.Event class.
		alert(event.key); //returns the lowercase letter pressed
		alert(event.shift); //returns true if the key pressed is shift
		if (event.key == 's' && event.control) alert('document saved');
	};
	(end)
*/

Moo.Event = new Moo.Class({

	initialize: function(event){
		event = event || window.event;
		this.event = event;
		this.type = event.type;
		this.target = event.target || event.srcElement;
		if (this.target.nodeType == 3) this.target = this.target.parentNode;
		this.shift = event.shiftKey;
		this.control = event.ctrlKey;
		this.alt = event.altKey;
		this.meta = event.metaKey;
		if (['DOMMouseScroll', 'mousewheel'].contains(this.type)){
			this.wheel = (event.wheelDelta) ? event.wheelDelta / 120 : -(event.detail || 0) / 3;
		} else if (this.type.contains('key')){
			this.code = event.which || event.keyCode;
			for (var name in Moo.Event.keys){
				if (Moo.Event.keys[name] == this.code){
					this.key = name;
					break;
				}
			}
			if (this.type == 'keydown'){
				var fKey = this.code - 111;
				if (fKey > 0 && fKey < 13) this.key = 'f' + fKey;
			}
			this.key = this.key || String.fromCharCode(this.code).toLowerCase();
		} else if (this.type.test(/(click|mouse|menu)/)){
			this.page = {
				'x': event.pageX || event.clientX + document.documentElement.scrollLeft,
				'y': event.pageY || event.clientY + document.documentElement.scrollTop
			};
			this.client = {
				'x': event.pageX ? event.pageX - window.pageXOffset : event.clientX,
				'y': event.pageY ? event.pageY - window.pageYOffset : event.clientY
			};
			this.rightClick = (event.which == 3) || (event.button == 2);
			switch(this.type){
				case 'mouseover': this.relatedTarget = event.relatedTarget || event.fromElement; break;
				case 'mouseout': this.relatedTarget = event.relatedTarget || event.toElement;
			}
			if (this.relatedTarget && this.relatedTarget.nodeType == 3) this.relatedTarget = this.relatedTarget.parentNode;
		}
	},

	/*
	Property: stop
		cross browser method to stop an event
	*/

	stop: function() {
		return this.stopPropagation().preventDefault();
	},

	/*
	Property: stopPropagation
		cross browser method to stop the propagation of an event
	*/

	stopPropagation: function(){
		if (this.event.stopPropagation) this.event.stopPropagation();
		else this.event.cancelBubble = true;
		return this;
	},

	/*
	Property: preventDefault
		cross browser method to prevent the default action of the event
	*/

	preventDefault: function(){
		if (this.event.preventDefault) this.event.preventDefault();
		else this.event.returnValue = false;
		return this;
	}

});

Moo.Event.keys = new Moo.Abstract({
	'enter': 13,
	'up': 38,
	'down': 40,
	'left': 37,
	'right': 39,
	'esc': 27,
	'space': 32,
	'backspace': 8,
	'tab': 9,
	'delete': 46
});

/* Section: Custom Moo.Events */

Moo.Element.Events.extend({

	/*	Moo.Event: mouseenter
			In addition to the standard javascript events (load, mouseover, mouseout, click, etc.) <Moo.Event.js> contains two custom events
			this event fires when the mouse enters the area of the dom element; will not be fired again if the mouse crosses over children of the element (unlike mouseover)
		

		Example:
			>Moo.$(myElement).addEvent('mouseenter', myFunction);
	*/

	'mouseenter': {
		type: 'mouseover',
		map: function(event){
			event = new Moo.Event(event);
			if (event.relatedTarget == this || this.hasChild(event.relatedTarget)) return;
			this.fireEvent('mouseenter', event);
		}
	},
	
	/*	Moo.Event: mouseleave
			this event fires when the mouse exits the area of the dom element; will not be fired again if the mouse crosses over children of the element (unlike mouseout)
		

		Example:
			>Moo.$(myElement).addEvent('mouseleave', myFunction);
	*/
	
	'mouseleave': {
		type: 'mouseout',
		map: function(event){
			event = new Moo.Event(event);
			if (event.relatedTarget == this || this.hasChild(event.relatedTarget)) return;
			this.fireEvent('mouseleave', event);
		}
	}
	
});

/*
Moo.Class: Function
	A collection of The Function Object prototype methods.
*/

Function.extend({

	/*
	Property: bindWithEvent
		automatically passes mootools Moo.Event Moo.Class.

	Arguments:
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, an argument to pass to the function; if more than one argument, it must be an array of arguments.

	Returns:
		a function with the parameter bind as its "this" and as a pre-passed argument event or window.event, depending on the browser.

	Example:
		>function myFunction(event){
		>	alert(event.client.x) //returns the coordinates of the mouse..
		>};
		>myElement.onclick = myFunction.bindWithEvent(myElement);
	*/

	bindWithEvent: function(bind, args){
		return this.create({'bind': bind, 'arguments': args, 'event': Moo.Event});
	}

});

/*
Script: Dom.js
	Css Query related function and <Moo.Element> extensions

License:
	MIT-style license.
*/

/* Section: Utility Functions */

/*
Function: Moo.$E
	Selects a single (i.e. the first found) Moo.Element based on the selector passed in and an optional filter element.
	Returns as <Moo.Element>.

Arguments:
	selector - string; the css selector to match
	filter - optional; a DOM element to limit the scope of the selector match; defaults to document.

Example:
	>Moo.$E('a', 'myElement') //find the first anchor tag inside the DOM element with id 'myElement'

Returns:
	a DOM element - the first element that matches the selector
*/

Moo.$E = function(selector, filter){
	return (Moo.$(filter) || document).getElement(selector);
};

/*
Function: Moo.$ES
	Returns a collection of Moo.Elements that match the selector passed in limited to the scope of the optional filter.
	See Also: <Moo.Element.getElements> for an alternate syntax.
	Returns as <Moo.Elements>.

Returns:
	an array of dom elements that match the selector within the filter

Arguments:
	selector - string; css selector to match
	filter - optional; a DOM element to limit the scope of the selector match; defaults to document.

Examples:
	>Moo.$ES("a") //gets all the anchor tags; synonymous with Moo.$$("a")
	>Moo.$ES('a','myElement') //get all the anchor tags within Moo.$('myElement')
*/

Moo.$ES = function(selector, filter){
	return (Moo.$(filter) || document).getElementsBySelector(selector);
};

Moo.$$.shared = {

	cache: {},

	regexp: /^(\w*|\*)(?:#([\w-]+)|\.([\w-]+))?(?:\[(\w+)(?:([!*^$]?=)["']?([^"'\]]*)["']?)?])?$/,

	getNormalParam: function(selector, items, context, param, i){
		Moo.Filters.selector = param;
		if (i == 0){
			if (param[2]){
				var el = context.getElementById(param[2]);
				if (!el || ((param[1] != '*') && (el.tagName.toLowerCase() != param[1]))) return false;
				items = [el];
			} else {
				items = Moo.$A(context.getElementsByTagName(param[1]));
			}
		} else {
			items = Moo.$$.shared.getElementsByTagName(items, param[1]);
			if (param[2]) items = items.filter(Moo.Filters.id);
		}
		if (param[3]) items = items.filter(Moo.Filters.className);
		if (param[4]) items = items.filter(Moo.Filters.attribute);
		return items;
	},

	getXpathParam: function(selector, items, context, param, i){
		if (Moo.$$.shared.cache[selector].xpath){
			items.push(Moo.$$.shared.cache[selector].xpath);
			return items;
		}
		var temp = context.namespaceURI ? ['xhtml:'] : [];
		temp.push(param[1]);
		if (param[2]) temp.push('[@id="', param[2], '"]');
		if (param[3]) temp.push('[contains(concat(" ", @class, " "), " ', param[3], ' ")]');
		if (param[4]){
			if (param[5] && param[6]){
				switch(param[5]){
					case '*=': temp.push('[contains(@', param[4], ', "', param[6], '")]'); break;
					case '^=': temp.push('[starts-with(@', param[4], ', "', param[6], '")]'); break;
					case 'Moo.$=': temp.push('[substring(@', param[4], ', string-length(@', param[4], ') - ', param[6].length, ' + 1) = "', param[6], '"]'); break;
					case '=': temp.push('[@', param[4], '="', param[6], '"]'); break;
					case '!=': temp.push('[@', param[4], '!="', param[6], '"]');
				}
			} else {
				temp.push('[@', param[4], ']');
			}
		}
		temp = temp.join('');
		Moo.$$.shared.cache[selector].xpath = temp;
		items.push(temp);
		return items;
	},

	getNormalItems: function(items, context, nocash){
		return (nocash) ? items : Moo.$$.unique(items);
	},

	getXpathItems: function(items, context, nocash){
		var elements = [];
		var xpath = document.evaluate('.//' + items.join('//'), context, Moo.$$.shared.resolver, XPathResult.UNORDERED_NODE_SNAPSHOT_TYPE, null);
		for (var i = 0, j = xpath.snapshotLength; i < j; i++) elements.push(xpath.snapshotItem(i));
		return (nocash) ? elements : Moo.$extend(elements.map(Moo.$), new Moo.Elements);
	},

	resolver: function(prefix){
		return (prefix == 'xhtml') ? 'http://www.w3.org/1999/xhtml' : false;
	},

	getElementsByTagName: function(context, tagName){
		var found = [];
		for (var i = 0, j = context.length; i < j; i++) found = found.concat(Moo.$A(context[i].getElementsByTagName(tagName)));
		return found;
	}

};

if (window.xpath){
	Moo.$$.shared.getParam = Moo.$$.shared.getXpathParam;
	Moo.$$.shared.getItems = Moo.$$.shared.getXpathItems;
} else {
	Moo.$$.shared.getParam = Moo.$$.shared.getNormalParam;
	Moo.$$.shared.getItems = Moo.$$.shared.getNormalItems;
}

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.domMethods = {

	/*
	Property: getElements
		Gets all the elements within an element that match the given (single) selector.
		Returns as <Moo.Elements>.

	Arguments:
		selector - string; the css selector to match

	Examples:
		>Moo.$('myElement').getElements('a'); // get all anchors within myElement
		>Moo.$('myElement').getElements('input[name=dialog]') //get all input tags with name 'dialog'
		>Moo.$('myElement').getElements('input[name$=log]') //get all input tags with names ending with 'log'

	Notes:
		Supports these operators in attribute selectors:

		- = : is equal to
		- ^= : starts-with
		- Moo.$= : ends-with
		- != : is not equal to

		Xpath is used automatically for compliant browsers.
	*/

	getElements: function(selector, nocash){
		var items = [];
		selector = selector.trim().split(' ');
		for (var i = 0, j = selector.length; i < j; i++){
			var sel = selector[i];
			var param;
			if (Moo.$$.shared.cache[sel]){
				param = Moo.$$.shared.cache[sel].param;
			} else {
				param = sel.match(Moo.$$.shared.regexp);
				if (!param) break;
				param[1] = param[1] || '*';
				Moo.$$.shared.cache[sel] = {'param': param};
			}
			var temp = Moo.$$.shared.getParam(sel, items, this, param, i);
			if (!temp) break;
			items = temp;
		}
		return Moo.$$.shared.getItems(items, this, nocash);
	},

	/*
	Property: getElement
		Same as <Moo.Element.getElements>, but returns only the first. Alternate syntax for <Moo.$E>, where filter is the Moo.Element.
		Returns as <Moo.Element>.

	Arguments:
		selector - string; css selector
	*/

	getElement: function(selector){
		return Moo.$(this.getElements(selector, true)[0] || false);
	},

	/*
	Property: getElementsBySelector
		Same as <Moo.Element.getElements>, but allows for comma separated selectors, as in css. Alternate syntax for <Moo.$$>, where filter is the Moo.Element.
		Returns as <Moo.Elements>.

	Arguments:
		selector - string; css selector
	*/

	getElementsBySelector: function(selector, nocash){
		var elements = [];
		selector = selector.split(',');
		for (var i = 0, j = selector.length; i < j; i++) elements = elements.concat(this.getElements(selector[i], true));
		return (nocash) ? elements : Moo.$$.unique(elements);
	},

	/*
	Property: getElementsByClassName
		Returns all the elements that match a specific class name.
		Here for compatibility purposes. can also be written: document.getElements('.className'), or Moo.$$('.className')
		Returns as <Moo.Elements>.

	Arguments:
		className - string; css classname
	*/

	getElementsByClassName: function(className){
		return this.getElements('.' + className);
	}

};

Moo.Element.extend({

	/*
	Property: getElementById
		Targets an element with the specified id found inside the Moo.Element. Does not overwrite document.getElementById.

	Arguments:
		id - string; the id of the element to find.
	*/

	getElementById: function(id){
		var el = document.getElementById(id);
		if (!el) return false;
		for (var parent = el.parentNode; parent != this; parent = parent.parentNode){
			if (!parent) return false;
		}
		return el;
	}

});

document.extend(Moo.Element.domMethods);
Moo.Element.extend(Moo.Element.domMethods);

//dom filters, internal methods.

Moo.Filters = {

	selector: [],

	id: function(el){
		return (el.id == Moo.Filters.selector[2]);
	},

	className: function(el){
		return el.className.contains(Moo.Filters.selector[3], ' ');
	},

	attribute: function(el){
		var current = Moo.Element.prototype.getProperty.call(el, Moo.Filters.selector[4]);
		if (!current) return false;
		var operator = Moo.Filters.selector[5];
		if (!operator) return true;
		var value = Moo.Filters.selector[6];
		switch(operator){
			case '=': return (current == value);
			case '*=': return (current.contains(value));
			case '^=': return (current.test('^' + value));
			case 'Moo.$=': return (current.test(value + 'Moo.$'));
			case '!=': return (current != value);
			case '~=': return current.contains(value, ' ');
		}
		return false;
	}

};

/*
Script: Moo.Element.Form.js
	Contains Moo.Element prototypes to deal with Forms and their elements.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.extend({

	/*
	Property: getValue
		Returns the value of the Moo.Element, if its tag is textarea, select or input. getValue called on a multiple select will return an array.
	*/

	getValue: function(){
		switch(this.getTag()){
			case 'select':
				var values = [];
				Moo.$each(this.options, function(opt){
					if (opt.selected) values.push(Moo.$pick(opt.value, opt.text));
				});
				return (this.multiple) ? values : values[0];
			case 'input': if (!(this.checked && ['checkbox', 'radio'].contains(this.type)) && !['hidden', 'text', 'password'].contains(this.type)) break;
			case 'textarea': return this.value;
		}
		return false;
	},

	getFormElements: function(){
		return Moo.$$(this.getElementsByTagName('input'), this.getElementsByTagName('select'), this.getElementsByTagName('textarea'));
	},

	/*
	Property: toQueryString
		Reads the children inputs of the Moo.Element and generates a query string, based on their values. Used internally in <Moo.Ajax>

	Example:
		(start code)
		<form id="myForm" action="submit.php">
		<input name="email" value="bob@bob.com">
		<input name="zipCode" value="90210">
		</form>

		<script>
		 Moo.$('myForm').toQueryString()
		</script>
		(end)

		Returns:
			email=bob@bob.com&zipCode=90210
	*/

	toQueryString: function(){
		var queryString = [];
		this.getFormElements().each(function(el){
			var name = el.name;
			var value = el.getValue();
			if (value === false || !name || el.disabled) return;
			var qs = function(val){
				queryString.push(name + '=' + encodeURIComponent(val));
			};
			if (Moo.$type(value) == 'array') value.each(qs);
			else qs(value);
		});
		return queryString.join('&');
	}

});

/*
Script: Moo.Element.Dimensions.js
	Contains Moo.Element prototypes to deal with Moo.Element size and position in space.
	
Note:
	The functions in this script require n XHTML doctype.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.extend({
	
	/*
	Property: scrollTo
		Scrolls the element to the specified coordinated (if the element has an overflow)

	Arguments:
		x - the x coordinate
		y - the y coordinate

	Example:
		>Moo.$('myElement').scrollTo(0, 100)
	*/

	scrollTo: function(x, y){
		this.scrollLeft = x;
		this.scrollTop = y;
	},

	/*
	Property: getSize
		Return an Object representing the size/scroll values of the element.

	Example:
		(start code)
		Moo.$('myElement').getSize();
		(end)

	Returns:
		(start code)
		{
			'scroll': {'x': 100, 'y': 100},
			'size': {'x': 200, 'y': 400},
			'scrollSize': {'x': 300, 'y': 500}
		}
		(end)
	*/

	getSize: function(){
		return {
			'scroll': {'x': this.scrollLeft, 'y': this.scrollTop},
			'size': {'x': this.offsetWidth, 'y': this.offsetHeight},
			'scrollSize': {'x': this.scrollWidth, 'y': this.scrollHeight}
		};
	},

	/*
	Property: getPosition
		Returns the real offsets of the element.

	Example:
		>Moo.$('element').getPosition();

	Returns:
		>{x: 100, y:500};
	*/

	getPosition: function(overflown){
		overflown = overflown || [];
		var el = this, left = 0, top = 0;
		do {
			left += el.offsetLeft || 0;
			top += el.offsetTop || 0;
			el = el.offsetParent;
		} while (el);
		overflown.each(function(element){
			left -= element.scrollLeft || 0;
			top -= element.scrollTop || 0;
		});
		return {'x': left, 'y': top};
	},

	/*
	Property: getTop
		Returns the distance from the top of the window to the Moo.Element.
	*/

	getTop: function(){
		return this.getPosition().y;
	},

	/*
	Property: getLeft
		Returns the distance from the left of the window to the Moo.Element.
	*/

	getLeft: function(){
		return this.getPosition().x;
	},

	/*
	Property: getCoordinates
		Returns an object with width, height, left, right, top, and bottom, representing the values of the Moo.Element

	Example:
		(start code)
		var myValues = Moo.$('myElement').getCoordinates();
		(end)

	Returns:
		(start code)
		{
			width: 200,
			height: 300,
			left: 100,
			top: 50,
			right: 300,
			bottom: 350
		}
		(end)
	*/

	getCoordinates: function(overflown){
		var position = this.getPosition(overflown);
		var obj = {
			'width': this.offsetWidth,
			'height': this.offsetHeight,
			'left': position.x,
			'top': position.y
		};
		obj.right = obj.left + obj.width;
		obj.bottom = obj.top + obj.height;
		return obj;
	}

});

/*
Script: Moo.Element.Events.js
	Contains Moo.Element prototypes to deal with Moo.Element events.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.eventMethods = {

	/*
	Property: addEvent
		Attaches an event listener to a DOM element.

	Arguments:
		type - the event to monitor ('click', 'load', etc) without the prefix 'on'.
		fn - the function to execute

	Example:
		>Moo.$('myElement').addEvent('click', function(){alert('clicked!')});
	*/

	addEvent: function(type, fn){
		this.$events = this.$events || {};
		this.$events[type] = this.$events[type] || {'keys': [], 'values': []};
		if (this.$events[type].keys.contains(fn)) return this;
		this.$events[type].keys.push(fn);
		var realType = type;
		var bound = false;
		if (Moo.Element.Events[type]){
			if (Moo.Element.Events[type].add) Moo.Element.Events[type].add.call(this, fn);
			if (Moo.Element.Events[type].map) bound = Moo.Element.Events[type].map.bindAsEventListener(this);
			realType = Moo.Element.Events[type].type || type;
		}
		if (!this.addEventListener) bound = bound || fn.bindAsEventListener(this);
		else bound = bound || fn;
		this.$events[type].values.push(bound);
		return this.addListener(realType, bound);
	},

	/*
	Property: removeEvent
		Works as Moo.Element.addEvent, but instead removes the previously added event listener.
	*/

	removeEvent: function(type, fn){
		if (!this.$events || !this.$events[type]) return this;
		var pos = this.$events[type].keys.indexOf(fn);
		if (pos == -1) return this;
		var key = this.$events[type].keys.splice(pos,1)[0];
		var value = this.$events[type].values.splice(pos,1)[0];
		if (Moo.Element.Events[type]){
			if (Moo.Element.Events[type].remove) Moo.Element.Events[type].remove.call(this, fn);
			type = Moo.Element.Events[type].type || type;
		}
		return this.removeListener(type, value);
	},

	/*
	Property: addEvents
		As <addEvent>, but accepts an object and add multiple events at once.
	*/

	addEvents: function(source){
		return Moo.Element.setMany(this, 'addEvent', source);
	},

	/*
	Property: removeEvents
		removes all events of a certain type from an element. if no argument is passed in, removes all events.
	*/

	removeEvents: function(type){
		if (!this.$events) return this;
		if (type){
			if (this.$events[type]){
				Moo.$A(this.$events[type].keys).each(function(fn, i){
					this.removeEvent(type, fn);
				}, this);
				this.$events[type] = null;
			}
		} else {
			for (var evType in this.$events) this.removeEvents(evType);
			this.$events = null;
		}
		return this;
	},

	/*
	Property: fireEvent
		executes all events of the specified type present in the element.
	*/

	fireEvent: function(type, args){
		if (this.$events && this.$events[type]){
			this.$events[type].keys.each(function(fn){
				fn.bind(this, args)();
			}, this);
		}
	}

};

Moo.Element.Events.mousewheel = {
	type: (window.gecko) ? 'DOMMouseScroll' : 'mousewheel'
};

window.extend(Moo.Element.eventMethods);
document.extend(Moo.Element.eventMethods);
Moo.Element.extend(Moo.Element.eventMethods);

/*
Script: Moo.Window.DomReady.js
	Contains the custom event domready, for window.

License:
	MIT-style license.
*/

/* Section: Custom Moo.Events */

/*
Moo.Event: domready
	executes a function when the dom tree is loaded, without waiting for images. Only works when called from window. 

Credits:
	(c) Dean Edwards/Matthias Miller/John Resig, remastered for mootools.

Arguments:
	fn - the function to execute when the DOM is ready

Example:
	> window.addEvent('domready', function(){
	>	alert('the dom is ready');
	> });
*/

Moo.Element.Events.domready = {

	add: function(fn){
		if (window.loaded){
			fn.call(this);
			return;
		}
		var domReady = function(){
			if (window.loaded) return;
			window.loaded = true;
			window.timer = Moo.$clear(window.timer);
			this.fireEvent('domready');
		}.bind(this);
		if (document.readyState && window.khtml){
			window.timer = function(){
				if (['loaded','complete'].contains(document.readyState)) domReady();
			}.periodical(50);
		} else if (document.readyState && window.ie){
			if (!Moo.$('ie_ready')){
				var src = (window.location.protocol == 'https:') ? '://0' : 'javascript:void(0)';
				document.write('<script id="ie_ready" defer src="' + src + '"><\/script>');
				Moo.$('ie_ready').onreadystatechange = function(){
					if (this.readyState == 'complete') domReady();
				};
			}
		} else {
			window.addListener("load", domReady);
			document.addListener("DOMContentLoaded", domReady);
		}
	}

};

/* Section: Utility Functions */

/*
Function: window.onDomReady
	DEPRECATED: Executes the passed in function when the DOM is ready (when the document tree has loaded, not waiting for images).
	Same as <window.addEvent> ('domready', init).
*/

window.onDomReady = function(fn){
	return this.addEvent('domready', fn);
};

/*
Script: Moo.Window.Size.js
	Moo.Window cross-browser dimensions methods.
	
Note:
	The Functions in this script require an XHTML doctype.

License:
	MIT-style license.
*/

/*
Moo.Class: window
	Cross browser methods to get various window dimensions.
	Warning: All these methods require that the browser operates in strict mode, not quirks mode.
*/

window.extend({

	/*
	Property: getWidth
		Returns an integer representing the width of the browser window (without the scrollbar).
	*/

	getWidth: function(){
		if (this.khtml) return this.innerWidth;
		if (this.opera) return document.body.clientWidth;
		return document.documentElement.clientWidth;
	},

	/*
	Property: getHeight
		Returns an integer representing the height of the browser window (without the scrollbar).
	*/

	getHeight: function(){
		if (this.khtml) return this.innerHeight;
		if (this.opera) return document.body.clientHeight;
		return document.documentElement.clientHeight;
	},

	/*
	Property: getScrollWidth
		Returns an integer representing the scrollWidth of the window.
		This value is equal to or bigger than <getWidth>.

	See Also:
		<http://developer.mozilla.org/en/docs/DOM:element.scrollWidth>
	*/

	getScrollWidth: function(){
		if (this.ie) return Math.max(document.documentElement.offsetWidth, document.documentElement.scrollWidth);
		if (this.khtml) return document.body.scrollWidth;
		return document.documentElement.scrollWidth;
	},

	/*
	Property: getScrollHeight
		Returns an integer representing the scrollHeight of the window.
		This value is equal to or bigger than <getHeight>.

	See Also:
		<http://developer.mozilla.org/en/docs/DOM:element.scrollHeight>
	*/

	getScrollHeight: function(){
		if (this.ie) return Math.max(document.documentElement.offsetHeight, document.documentElement.scrollHeight);
		if (this.khtml) return document.body.scrollHeight;
		return document.documentElement.scrollHeight;
	},

	/*
	Property: getScrollLeft
		Returns an integer representing the scrollLeft of the window (the number of pixels the window has scrolled from the left).

	See Also:
		<http://developer.mozilla.org/en/docs/DOM:element.scrollLeft>
	*/

	getScrollLeft: function(){
		return this.pageXOffset || document.documentElement.scrollLeft;
	},

	/*
	Property: getScrollTop
		Returns an integer representing the scrollTop of the window (the number of pixels the window has scrolled from the top).

	See Also:
		<http://developer.mozilla.org/en/docs/DOM:element.scrollTop>
	*/

	getScrollTop: function(){
		return this.pageYOffset || document.documentElement.scrollTop;
	},

	/*
	Property: getSize
		Same as <Moo.Element.getSize>
	*/

	getSize: function(){
		return {
			'size': {'x': this.getWidth(), 'y': this.getHeight()},
			'scrollSize': {'x': this.getScrollWidth(), 'y': this.getScrollHeight()},
			'scroll': {'x': this.getScrollLeft(), 'y': this.getScrollTop()}
		};
	},

	//ignore
	getPosition: function(){return {'x': 0, 'y': 0}}

});

/*
Script: Moo.Fx.Base.js
	Contains <Moo.Fx.Base> and two Transitions.

License:
	MIT-style license.
*/

Moo.Fx = {Shared: {}};

/*
Moo.Class: Moo.Fx.Base
	Base class for the Mootools Effects (Moo.Fx) library.

Moo.Options:
	onStart - the function to execute as the effect begins; nothing (<Moo.Class.empty>) by default.
	onComplete - the function to execute after the effect has processed; nothing (<Moo.Class.empty>) by default.
	transition - the equation to use for the effect see <Moo.Fx.Transitions>; default is <Moo.Fx.Transitions.Sine.easeInOut>
	duration - the duration of the effect in ms; 500 is the default.
	unit - the unit is 'px' by default (other values include things like 'em' for fonts or '%').
	wait - boolean: to wait or not to wait for a current transition to end before running another of the same instance. defaults to true.
	fps - the frames per second for the transition; default is 30
*/

Moo.Fx.Base = new Moo.Class({

	options: {
		onStart: Moo.Class.empty,
		onComplete: Moo.Class.empty,
		onCancel: Moo.Class.empty,
		transition: function(t, c, d){
			return -c / 2 * (Math.cos(Math.PI * t / d) - 1);
		},
		duration: 500,
		unit: 'px',
		wait: true,
		fps: 50
	},

	initialize: function(options){
		this.element = this.element || null;
		this.setOptions(options);
		if (this.options.initialize) this.options.initialize.call(this);
	},

	step: function(){
		var time = Moo.$time();
		if (time < this.time + this.options.duration){
			this.cTime = time - this.time;
			this.setNow();
			this.increase();
		} else {
			this.stop(true);
			this.now = this.to;
			this.increase();
			this.fireEvent('onComplete', this.element, 10);
			this.callChain();
		}
	},

	/*
	Property: set
		Immediately sets the value with no transition.

	Arguments:
		to - the point to jump to

	Example:
		>var myFx = new Moo.Fx.Style('myElement', 'opacity').set(0); //will make it immediately transparent
	*/

	set: function(to){
		this.now = to;
		this.increase();
		return this;
	},

	setNow: function(){
		this.now = this.compute(this.from, this.to);
	},

	compute: function(from, to){
		return this.options.transition(this.cTime, (to - from), this.options.duration) + from;
	},

	/*
	Property: start
		Executes an effect from one position to the other.

	Arguments:
		from - integer: staring value
		to - integer: the ending value

	Examples:
		>var myFx = new Moo.Fx.Style('myElement', 'opacity').start(0,1); //display a transition from transparent to opaque.
	*/

	start: function(from, to){
		if (!this.options.wait) this.stop();
		else if (this.timer) return this;
		this.from = from;
		this.to = to;
		this.time = Moo.$time();
		this.timer = this.step.periodical(Math.round(1000 / this.options.fps), this);
		this.fireEvent('onStart', this.element);
		return this;
	},

	/*
	Property: stop
		Stops the transition.
	*/

	stop: function(end){
		if (!this.timer) return this;
		this.timer = Moo.$clear(this.timer);
		if (!end) this.fireEvent('onCancel', this.element);
		return this;
	},

	//compat
	custom: function(from, to){return this.start(from, to)},
	clearTimer: function(end){return this.stop(end)}

});

Moo.Fx.Base.implement(new Moo.Chain);
Moo.Fx.Base.implement(new Moo.Events);
Moo.Fx.Base.implement(new Moo.Options);

/*
Script: Moo.Fx.CSS.js
	Css parsing class for effects. Required by <Moo.Fx.Style>, <Moo.Fx.Styles>, <Moo.Fx.Elements>. No documentation needed, as its used internally.

License:
	MIT-style license.
*/

Moo.Fx.CSS = {

	select: function(property, to){
		if (property.test(/color/i)) return this.Color;
		if (to.contains && to.contains(' ')) return this.Multi;
		return this.Single;
	},

	parse: function(el, property, fromTo){
		if (!fromTo.push) fromTo = [fromTo];
		var from = fromTo[0], to = fromTo[1];
		if (!to && to != 0){
			to = from;
			from = el.getStyle(property);
		}
		var css = this.select(property, to);
		return {from: css.parse(from), to: css.parse(to), css: css};
	}

};

Moo.Fx.CSS.Single = {

	parse: function(value){
		return parseFloat(value);
	},

	getNow: function(from, to, fx){
		return fx.compute(from, to);
	},

	getValue: function(value, unit){
		return value + unit;
	}

};

Moo.Fx.CSS.Multi = {

	parse: function(value){
		return value.push ? value : value.split(' ').map(function(v){
			return parseFloat(v);
		});
	},

	getNow: function(from, to, fx){
		var now = [];
		for (var i = 0; i < from.length; i++) now[i] = fx.compute(from[i], to[i]);
		return now;
	},

	getValue: function(value, unit){
		return value.join(unit + ' ') + unit;
	}

};

Moo.Fx.CSS.Color = {

	parse: function(value){
		return value.push ? value : value.hexToRgb(true);
	},

	getNow: function(from, to, fx){
		var now = [];
		for (var i = 0; i < from.length; i++) now[i] = Math.round(fx.compute(from[i], to[i]));
		return now;
	},

	getValue: function(value){
		return 'rgb(' + value.join(',') + ')';
	}

};

/*
Script: Moo.Fx.Style.js
	Contains <Moo.Fx.Style>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Fx.Style
	The Style effect; Extends <Moo.Fx.Base>, inherits all its properties. Used to transition any css property from one value to another. Includes colors.
	Colors must be in hex format.

Arguments:
	el - the Moo.$(element) to apply the style transition to
	property - the property to transition
	options - the Moo.Fx.Base options (see: <Moo.Fx.Base>)

Example:
	>var marginChange = new Moo.Fx.Style('myElement', 'margin-top', {duration:500});
	>marginChange.start(10, 100);
*/

Moo.Fx.Style = Moo.Fx.Base.extend({

	initialize: function(el, property, options){
		this.element = Moo.$(el);
		this.property = property;
		this.parent(options);
	},

	/*
	Property: hide
		Same as <Moo.Fx.Base.set> (0); hides the element immediately without transition.
	*/

	hide: function(){
		return this.set(0);
	},

	setNow: function(){
		this.now = this.css.getNow(this.from, this.to, this);
	},

	/*
	Property: set
		Sets the element's css property (specified at instantiation) to the specified value immediately.

	Example:
		(start code)
		var marginChange = new Moo.Fx.Style('myElement', 'margin-top', {duration:500});
		marginChange.set(10); //margin-top is set to 10px immediately
		(end)
	*/

	set: function(to){
		this.css = Moo.Fx.CSS.select(this.property, to);
		return this.parent(this.css.parse(to));
	},

	/*
	Property: start
		Displays the transition to the value/values passed in

	Arguments:
		from - (integer; optional) the starting position for the transition
		to - (integer) the ending position for the transition

	Note:
		If you provide only one argument, the transition will use the current css value for its starting value.

	Example:
		(start code)
		var marginChange = new Moo.Fx.Style('myElement', 'margin-top', {duration:500});
		marginChange.start(10); //tries to read current margin top value and goes from current to 10
		(end)
	*/

	start: function(from, to){
		if (this.timer && this.options.wait) return this;
		var parsed = Moo.Fx.CSS.parse(this.element, this.property, [from, to]);
		this.css = parsed.css;
		return this.parent(parsed.from, parsed.to);
	},

	increase: function(){
		this.element.setStyle(this.property, this.css.getValue(this.now, this.options.unit));
	}

});

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.extend({

	/*
	Property: effect
		Applies an <Moo.Fx.Style> to the Moo.Element; This a shortcut for <Moo.Fx.Style>.
	
	Arguments:
		property - (string) the css property to alter
		options - (object; optional) key/value set of options (see <Moo.Fx.Style>)

	Example:
		>var myEffect = Moo.$('myElement').effect('height', {duration: 1000, transition: Moo.Fx.Transitions.linear});
		>myEffect.start(10, 100);
		>//OR
		>Moo.$('myElement').effect('height', {duration: 1000, transition: Moo.Fx.Transitions.linear}).start(10,100);
	*/

	effect: function(property, options){
		return new Moo.Fx.Style(this, property, options);
	}

});

/*
Script: Moo.Fx.Styles.js
	Contains <Moo.Fx.Styles>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Fx.Styles
	Allows you to animate multiple css properties at once; Extends <Moo.Fx.Base>, inherits all its properties. Includes colors.
	Colors must be in hex format.

Arguments:
	el - the Moo.$(element) to apply the styles transition to
	options - the fx options (see: <Moo.Fx.Base>)

Example:
	(start code)
	var myEffects = new Moo.Fx.Styles('myElement', {duration: 1000, transition: Moo.Fx.Transitions.linear});

	//height from 10 to 100 and width from 900 to 300
	myEffects.start({
		'height': [10, 100],
		'width': [900, 300]
	});

	//or height from current height to 100 and width from current width to 300
	myEffects.start({
		'height': 100,
		'width': 300
	});
	(end)
*/

Moo.Fx.Styles = Moo.Fx.Base.extend({

	initialize: function(el, options){
		this.element = Moo.$(el);
		this.parent(options);
	},

	setNow: function(){
		for (var p in this.from) this.now[p] = this.css[p].getNow(this.from[p], this.to[p], this);
	},

	set: function(to){
		var parsed = {};
		this.css = {};
		for (var p in to){
			this.css[p] = Moo.Fx.CSS.select(p, to[p]);
			parsed[p] = this.css[p].parse(to[p]);
		}
		return this.parent(parsed);
	},

	/*
	Property: start
		Executes a transition for any number of css properties in tandem.

	Arguments:
		obj - an object containing keys that specify css properties to alter and values that specify either the from/to values (as an array) or just the end value (an integer).

	Example:
		see <Moo.Fx.Styles>
	*/

	start: function(obj){
		if (this.timer && this.options.wait) return this;
		this.now = {};
		this.css = {};
		var from = {}, to = {};
		for (var p in obj){
			var parsed = Moo.Fx.CSS.parse(this.element, p, obj[p]);
			from[p] = parsed.from;
			to[p] = parsed.to;
			this.css[p] = parsed.css;
		}
		return this.parent(from, to);
	},

	increase: function(){
		for (var p in this.now) this.element.setStyle(p, this.css[p].getValue(this.now[p], this.options.unit));
	}

});

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.extend({

	/*
	Property: effects
		Applies an <Moo.Fx.Styles> to the Moo.Element; This a shortcut for <Moo.Fx.Styles>.

	Example:
		>var myEffects = Moo.$(myElement).effects({duration: 1000, transition: Moo.Fx.Transitions.Sine.easeInOut});
 		>myEffects.start({'height': [10, 100], 'width': [900, 300]});
	*/

	effects: function(options){
		return new Moo.Fx.Styles(this, options);
	}

});

/*
Script: Moo.Fx.Elements.js
	Contains <Moo.Fx.Elements>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Fx.Elements
	Moo.Fx.Elements allows you to apply any number of styles transitions to a selection of elements. Includes colors (must be in hex format).

Arguments:
	elements - a collection of elements the effects will be applied to.
	options - same as <Moo.Fx.Base> options.
*/

Moo.Fx.Elements = Moo.Fx.Base.extend({

	initialize: function(elements, options){
		this.elements = Moo.$$(elements);
		this.parent(options);
	},

	setNow: function(){
		for (var i in this.from){
			var iFrom = this.from[i], iTo = this.to[i], iCss = this.css[i], iNow = this.now[i] = {};
			for (var p in iFrom) iNow[p] = iCss[p].getNow(iFrom[p], iTo[p], this);
		}
	},

	set: function(to){
		var parsed = {};
		this.css = {};
		for (var i in to){
			var iTo = to[i], iCss = this.css[i] = {}, iParsed = parsed[i] = {};
			for (var p in iTo){
				iCss[p] = Moo.Fx.CSS.select(p, iTo[p]);
				iParsed[p] = iCss[p].parse(iTo[p]);
			}
		}
		return this.parent(parsed);
	},

	/*
	Property: start
		Applies the passed in style transitions to each object named (see example). Each item in the collection is refered to as a numerical string ("1" for instance). The first item is "0", the second "1", etc.

	Example:
		(start code)
		var myElementsEffects = new Moo.Fx.Elements(Moo.$$('a'));
		myElementsEffects.start({
			'0': { //let's change the first element's opacity and width
				'opacity': [0,1],
				'width': [100,200]
			},
			'4': { //and the fifth one's opacity
				'opacity': [0.2, 0.5]
			}
		});
		(end)
	*/

	start: function(obj){
		if (this.timer && this.options.wait) return this;
		this.now = {};
		this.css = {};
		var from = {}, to = {};
		for (var i in obj){
			var iProps = obj[i], iFrom = from[i] = {}, iTo = to[i] = {}, iCss = this.css[i] = {};
			for (var p in iProps){
				var parsed = Moo.Fx.CSS.parse(this.elements[i], p, iProps[p]);
				iFrom[p] = parsed.from;
				iTo[p] = parsed.to;
				iCss[p] = parsed.css;
			}
		}
		return this.parent(from, to);
	},

	increase: function(){
		for (var i in this.now){
			var iNow = this.now[i], iCss = this.css[i];
			for (var p in iNow) this.elements[i].setStyle(p, iCss[p].getValue(iNow[p], this.options.unit));
		}
	}

});

/*
Script: Moo.Fx.Scroll.js
	Contains <Moo.Fx.Scroll>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Fx.Scroll
	Scroll any element with an overflow, including the window element.
	
Note:
	Moo.Fx.Scroll requires an XHTML doctype.

Arguments:
	element - the element to scroll
	options - same as <Moo.Fx.Base> options.
*/

Moo.Fx.Scroll = Moo.Fx.Base.extend({

	initialize: function(element, options){
		this.now = [];
		this.element = Moo.$(element);
		this.addEvent('onStart', function(){
			this.element.addEvent('mousewheel', this.stop.bind(this, false));
		}.bind(this));
		this.removeEvent('onComplete', function(){
			this.element.removeEvent('mousewheel', this.stop.bind(this, false));
		}.bind(this));
		this.parent(options);
	},

	setNow: function(){
		for (var i = 0; i < 2; i++) this.now[i] = this.compute(this.from[i], this.to[i]);
	},

	/*
	Property: scrollTo
		Scrolls the chosen element to the x/y coordinates.

	Arguments:
		x - the x coordinate to scroll the element to
		y - the y coordinate to scroll the element to
	*/

	scrollTo: function(x, y){
		if (this.timer && this.options.wait) return this;
		var el = this.element.getSize();
		var values = {'x': x, 'y': y};
		for (var z in el.size){
			var max = el.scrollSize[z] - el.size[z];
			if (Moo.$chk(values[z])) values[z] = (Moo.$type(values[z]) == 'number') ? Math.max(Math.min(values[z], max), 0) : max;
			else values[z] = el.scroll[z];
		}
		return this.start([el.scroll.x, el.scroll.y], [values.x, values.y]);
	},

	/*
	Property: toTop
		Scrolls the chosen element to its maximum top.
	*/

	toTop: function(){
		return this.scrollTo(false, 0);
	},

	/*
	Property: toBottom
		Scrolls the chosen element to its maximum bottom.
	*/

	toBottom: function(){
		return this.scrollTo(false, 'full');
	},

	/*
	Property: toLeft
		Scrolls the chosen element to its maximum left.
	*/

	toLeft: function(){
		return this.scrollTo(0, false);
	},

	/*
	Property: toRight
		Scrolls the chosen element to its maximum right.
	*/

	toRight: function(){
		return this.scrollTo('full', false);
	},

	/*
	Property: toElement
		Scrolls the specified element to the position the passed in element is found.

	Arguments:
		el - the Moo.$(element) to scroll the window to
	*/

	toElement: function(el){
		var parent = this.element.getPosition();
		var target = Moo.$(el).getPosition();
		return this.scrollTo(target.x - parent.x, target.y - parent.y);
	},

	increase: function(){
		this.element.scrollTo(this.now[0], this.now[1]);
	}

});

/*
Script: Moo.Fx.Slide.js
	Contains <Moo.Fx.Slide>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Fx.Slide
	The slide effect; slides an element in horizontally or vertically, the contents will fold inside. Extends <Moo.Fx.Base>, inherits all its properties.
	
Note:
	Moo.Fx.Slide requires an XHTML doctype.

Moo.Options:
	mode - set it to vertical or horizontal. Defaults to vertical.
	options - all the <Moo.Fx.Base> options

Example:
	(start code)
	var mySlider = new Moo.Fx.Slide('myElement', {duration: 500});
	mySlider.toggle() //toggle the slider up and down.
	(end)
*/

Moo.Fx.Slide = Moo.Fx.Base.extend({

	options: {
		mode: 'vertical'
	},

	initialize: function(el, options){
		this.element = Moo.$(el);
		this.wrapper = new Moo.Element('div', {'styles': Moo.$extend(this.element.getStyles('margin'), {'overflow': 'hidden'})}).injectAfter(this.element).adopt(this.element);
		this.element.setStyle('margin', 0);
		this.setOptions(options);
		this.now = [];
		this.parent(this.options);
	},

	setNow: function(){
		for (var i = 0; i < 2; i++) this.now[i] = this.compute(this.from[i], this.to[i]);
	},

	vertical: function(){
		this.margin = 'margin-top';
		this.layout = 'height';
		this.offset = this.element.offsetHeight;
	},

	horizontal: function(){
		this.margin = 'margin-left';
		this.layout = 'width';
		this.offset = this.element.offsetWidth;
	},

	/*
	Property: slideIn
		Slides the elements in view horizontally or vertically.

	Arguments:
		mode - (optional, string) 'horizontal' or 'vertical'; defaults to options.mode.
	*/

	slideIn: function(mode){
		this[mode || this.options.mode]();
		return this.start([this.element.getStyle(this.margin).toInt(), this.wrapper.getStyle(this.layout).toInt()], [0, this.offset]);
	},

	/*
	Property: slideOut
		Sides the elements out of view horizontally or vertically.

	Arguments:
		mode - (optional, string) 'horizontal' or 'vertical'; defaults to options.mode.
	*/

	slideOut: function(mode){
		this[mode || this.options.mode]();
		return this.start([this.element.getStyle(this.margin).toInt(), this.wrapper.getStyle(this.layout).toInt()], [-this.offset, 0]);
	},

	/*
	Property: hide
		Hides the element without a transition.

	Arguments:
		mode - (optional, string) 'horizontal' or 'vertical'; defaults to options.mode.
	*/

	hide: function(mode){
		this[mode || this.options.mode]();
		return this.set([-this.offset, 0]);
	},

	/*
	Property: show
		Shows the element without a transition.

	Arguments:
		mode - (optional, string) 'horizontal' or 'vertical'; defaults to options.mode.
	*/

	show: function(mode){
		this[mode || this.options.mode]();
		return this.set([0, this.offset]);
	},

	/*
	Property: toggle
		Slides in or Out the element, depending on its state

	Arguments:
		mode - (optional, string) 'horizontal' or 'vertical'; defaults to options.mode.

	*/

	toggle: function(mode){
		if (this.wrapper.offsetHeight == 0 || this.wrapper.offsetWidth == 0) return this.slideIn(mode);
		return this.slideOut(mode);
	},

	increase: function(){
		this.element.setStyle(this.margin, this.now[0] + this.options.unit);
		this.wrapper.setStyle(this.layout, this.now[1] + this.options.unit);
	}

});

/*
Script: Moo.Fx.Transitions.js
	Effects transitions, to be used with all the effects.

Author:
	Easing Equations by Robert Penner, <http://www.robertpenner.com/easing/>, modified & optimized to be used with mootools.

License:
	Easing Equations v1.5, (c) 2003 Robert Penner, all rights reserved. Open Source BSD License.
*/

/*
Moo.Class: Moo.Fx.Transitions
	A collection of tweening transitions for use with the <Moo.Fx.Base> classes.
	Some transitions accept additional parameters. You can set them using the .set property of each transition type.
	
Example:
	>new Moo.Fx.Style('margin', {transition: Moo.Fx.Transitions.Elastic.easeInOut});
	>//Elastic.easeInOut with default values
	>new Moo.Fx.Style('margin', {transition: Moo.Fx.Transitions.Elastic.easeInOut.set(3)});
	>//Elastic.easeInOut with user-defined value for elasticity.
	>p, t, c, d means: // p: current time / duration, t: current time, c: change in value (distance), d: duration

See also:
	http://www.robertpenner.com/easing/
*/

Moo.Fx.Transitions = new Moo.Abstract({
	
	/*
	Property: linear
		displays a linear transition.
		
	Graph:
		(see Linear.png)
	*/
	
	linear: function(t, c, d){
		return c * (t / d);
	}

});

Moo.Fx.Shared.CreateTransitionEases = function(transition, type){
	Moo.$extend(transition, {
		easeIn: function(t, c, d, x, y, z){
			return c - c * transition((d - t) / d, t, c, d, x, y, z);
		},

		easeOut: function(t, c, d, x, y, z){
			return c * transition(t / d, t, c, d, x, y, z);
		},

		easeInOut: function(t, c, d, x, y, z){
			d /= 2, c /= 2;
			var p = t / d;
			return (p < 1) ? transition.easeIn(t, c, d, x, y, z) : c * (transition(p - 1, t, c, d, x, y, z) + 1);
		}
	});
	//compatibility
	['In', 'Out', 'InOut'].each(function(mode){
		transition['ease' + mode].set = Moo.Fx.Shared.SetTransitionValues(transition['ease' + mode]);
		Moo.Fx.Transitions[type.toLowerCase() + mode] = transition['ease' + mode];
	});
};

Moo.Fx.Shared.SetTransitionValues = function(transition){
	return function(){
		var args = Moo.$A(arguments);
		return function(){
			return transition.apply(Moo.Fx.Transitions, Moo.$A(arguments).concat(args));
		};
	}
};

Moo.Fx.Transitions.extend = function(transitions){
	for (var type in transitions){
		if (type.test(/^[A-Z]/)) Moo.Fx.Shared.CreateTransitionEases(transitions[type], type);
		else transitions[type].set = Moo.Fx.Shared.SetTransitionValues(transitions[type]);
		Moo.Fx.Transitions[type] = transitions[type];
	}
};

Moo.Fx.Transitions.extend({
	
	/*
	Property: Sine
		displays a sineousidal transition. Must be used as Sine.easeIn or Sine.easeOut or Sine.easeInOut
		
	Graph:
		(see Sine.png)
	*/

	Sine: function(p){
		return Math.sin(p * (Math.PI / 2));
	},
	
	/*
	Property: Quad
		displays a quadratic transition. Must be used as Quad.easeIn or Quad.easeOut or Quad.easeInOut
		
	Graph:
		(see Quad.png)
	*/

	Quad: function(p){
		return -(Math.pow(p - 1, 2) - 1);
	},
	
	/*
	Property: Cubic
		displays a cubicular transition. Must be used as Cubic.easeIn or Cubic.easeOut or Cubic.easeInOut
		
	Graph:
		(see Cubic.png)
	*/

	Cubic: function(p){
		return Math.pow(p - 1, 3) + 1;
	},
	
	/*
	Property: Quart
		displays a quartetic transition. Must be used as Quart.easeIn or Quart.easeOut or Quart.easeInOut
		
	Graph:
		(see Quart.png)
	*/

	Quart: function(p){
		return -(Math.pow(p - 1, 4) - 1);
	},
	
	/*
	Property: Quint
		displays a quintic transition. Must be used as Quint.easeIn or Quint.easeOut or Quint.easeInOut
		
	Graph:
		(see Quint.png)
	*/

	Quint: function(p){
		return Math.pow(p - 1, 5) + 1;
	},
	
	/*
	Property: Expo
		displays a exponential transition. Must be used as Expo.easeIn or Expo.easeOut or Expo.easeInOut
		
	Graph:
		(see Expo.png)
	*/

	Expo: function(p){
		return -Math.pow(2, -10 * p) + 1;
	},
	
	/*
	Property: Circ
		displays a circular transition. Must be used as Circ.easeIn or Circ.easeOut or Circ.easeInOut
		
	Graph:
		(see Circ.png)
	*/

	Circ: function(p){
		return Math.sqrt(1 - Math.pow(p - 1, 2));
	},
	
	/*
	Property: Bounce
		makes the transition bouncy. Must be used as Bounce.easeIn or Bounce.easeOut or Bounce.easeInOut
		
	
	Graph:
		(see Bounce.png)
	*/

	Bounce: function(p){
		var b = 7.5625;
		if (p < (1 / 2.75)) return b * Math.pow(p, 2);
		else if (p < (2 / 2.75)) return b * (p -= (1.5 / 2.75)) * p + 0.75;
		else if (p < (2.5 / 2.75)) return b * (p -= (2.25 / 2.75)) * p + 0.9375;
		else return b * (p -= (2.625 / 2.75)) * p + 0.984375;
	},
	
	/*
	Property: Back
		makes the transition go back, then all forth. Must be used as Back.easeIn or Back.easeOut or Back.easeInOut
		set() changes the way it overshoots the target, default is 1.70158

	Graph:
		(see Back.png)
	*/

	Back: function(p, t, c, d, x){
		x = x || 1.70158;
		p -= 1;
		return Math.pow(p, 2) * ((x + 1) * p + x) + 1;
	},
	
	/*
	Property: Elastic
		Elastic curve. Must be used as Elastic.easeIn or Elastic.easeOut or Elastic.easeInOut
		set() works as a multiplier of the elasicity effect. set(2) makes it twice as strong
	
	Graph:
		(see Elastic.png)
	*/

	Elastic: function(p, t, c, d, x){
		x = d * 0.3 / (x || 1);
		return (c * Math.pow(2, -10 * p) * Math.sin((p * d - x / 4) * (2 * Math.PI) / x) + c) / c;
	}

});

/*
Script: Moo.Drag.Base.js
	Contains <Moo.Drag.Base>, <Moo.Element.makeResizable>

License:
	MIT-style license.
*/

Moo.Drag = {};

/*
Moo.Class: Moo.Drag.Base
	Modify two css properties of an element based on the position of the mouse.
	
Note:
	Moo.Drag.Base requires an XHTML doctype.

Arguments:
	el - the Moo.$(element) to apply the transformations to.
	options - optional. The options object.

Moo.Options:
	handle - the Moo.$(element) to act as the handle for the draggable element. defaults to the Moo.$(element) itself.
	modifiers - an object. see Modifiers Below.
	onStart - optional, function to execute when the user starts to drag (on mousedown);
	onComplete - optional, function to execute when the user completes the drag.
	onDrag - optional, function to execute at every step of the drag
	limit - an object, see Limit below.
	grid - optional, distance in px for snap-to-grid dragging
	snap - optional, the distance you have to drag before the element starts to respond to the drag. defaults to false

	modifiers:
		x - string, the style you want to modify when the mouse moves in an horizontal direction. defaults to 'left'
		y - string, the style you want to modify when the mouse moves in a vertical direction. defaults to 'top'

	limit:
		x - array with start and end limit relative to modifiers.x
		y - array with start and end limit relative to modifiers.y
*/

Moo.Drag.Base = new Moo.Class({

	options: {
		handle: false,
		unit: 'px',
		onStart: Moo.Class.empty,
		onBeforeStart: Moo.Class.empty,
		onComplete: Moo.Class.empty,
		onSnap: Moo.Class.empty,
		onDrag: Moo.Class.empty,
		limit: false,
		modifiers: {x: 'left', y: 'top'},
		grid: false,
		snap: 6
	},

	initialize: function(el, options){
		this.setOptions(options);
		this.element = Moo.$(el);
		this.handle = Moo.$(this.options.handle) || this.element;
		this.mouse = {'now': {}, 'pos': {}};
		this.value = {'start': {}, 'now': {}};
		this.bound = {
			'start': this.start.bindWithEvent(this),
			'check': this.check.bindWithEvent(this),
			'drag': this.drag.bindWithEvent(this),
			'stop': this.stop.bind(this)
		};
		this.attach();
		if (this.options.initialize) this.options.initialize.call(this);
	},

	attach: function(){
		this.handle.addEvent('mousedown', this.bound.start);
		return this;
	},

	detach: function(){
		this.handle.removeEvent('mousedown', this.bound.start);
		return this;
	},

	start: function(event){
		this.fireEvent('onBeforeStart', this.element);
		this.mouse.start = event.page;
		var limit = this.options.limit;
		this.limit = {'x': [], 'y': []};
		for (var z in this.options.modifiers){
			if (!this.options.modifiers[z]) continue;
			this.value.now[z] = this.element.getStyle(this.options.modifiers[z]).toInt();
			this.mouse.pos[z] = event.page[z] - this.value.now[z];
			if (limit && limit[z]){
				for (var i = 0; i < 2; i++){
					if (Moo.$chk(limit[z][i])) this.limit[z][i] = limit[z][i].apply ? limit[z][i].call(this) : limit[z][i];
				}
			}
		}
		if (Moo.$type(this.options.grid) == 'number') this.options.grid = {'x': this.options.grid, 'y': this.options.grid};
		document.addListener('mousemove', this.bound.check);
		document.addListener('mouseup', this.bound.stop);
		this.fireEvent('onStart', this.element);
		event.stop();
	},

	check: function(event){
		var distance = Math.round(Math.sqrt(Math.pow(event.page.x - this.mouse.start.x, 2) + Math.pow(event.page.y - this.mouse.start.y, 2)));
		if (distance > this.options.snap){
			document.removeListener('mousemove', this.bound.check);
			document.addListener('mousemove', this.bound.drag);
			this.drag(event);
			this.fireEvent('onSnap', this.element);
		}
		event.stop();
	},

	drag: function(event){
		this.out = false;
		this.mouse.now = event.page;
		for (var z in this.options.modifiers){
			if (!this.options.modifiers[z]) continue;
			this.value.now[z] = this.mouse.now[z] - this.mouse.pos[z];
			if (this.limit[z]){
				if (Moo.$chk(this.limit[z][1]) && (this.value.now[z] > this.limit[z][1])){
					this.value.now[z] = this.limit[z][1];
					this.out = true;
				} else if (Moo.$chk(this.limit[z][0]) && (this.value.now[z] < this.limit[z][0])){
					this.value.now[z] = this.limit[z][0];
					this.out = true;
				}
			}
			if (this.options.grid[z]) this.value.now[z] -= (this.value.now[z] % this.options.grid[z]);
			this.element.setStyle(this.options.modifiers[z], this.value.now[z] + this.options.unit);
		}
		this.fireEvent('onDrag', this.element);
		event.stop();
	},

	stop: function(){
		document.removeListener('mousemove', this.bound.check);
		document.removeListener('mousemove', this.bound.drag);
		document.removeListener('mouseup', this.bound.stop);
		this.fireEvent('onComplete', this.element);
	}

});

Moo.Drag.Base.implement(new Moo.Events);
Moo.Drag.Base.implement(new Moo.Options);

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.extend({

	/*
	Property: makeResizable
		Makes an element resizable (by dragging) with the supplied options.

	Arguments:
		options - see <Moo.Drag.Base> for acceptable options.
	*/

	makeResizable: function(options){
		return new Moo.Drag.Base(this, Moo.$merge({modifiers: {x: 'width', y: 'height'}}, options));
	}

});

/*
Script: Moo.Drag.Move.js
	Contains <Moo.Drag.Move>, <Moo.Element.makeDraggable>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Drag.Move
	Extends <Moo.Drag.Base>, has additional functionality for dragging an element, support snapping and droppables.
	Moo.Drag.move supports either position absolute or relative. If no position is found, absolute will be set.
	
Note:
	Moo.Drag.Move requires an XHTML doctype.

Arguments:
	el - the Moo.$(element) to apply the drag to.
	options - optional. see Moo.Options below.

Moo.Options:
	all the drag.Base options, plus:
	container - an element, will fill automatically limiting options based on the Moo.$(element) size and position. defaults to false (no limiting)
	droppables - an array of elements you can drop your draggable to.
*/

Moo.Drag.Move = Moo.Drag.Base.extend({

	options: {
		droppables: [],
		container: false,
		overflown: []
	},

	initialize: function(el, options){
		this.setOptions(options);
		this.element = Moo.$(el);
		this.position = this.element.getStyle('position');
		this.droppables = Moo.$$(this.options.droppables);
		if (!['absolute', 'relative'].contains(this.position)) this.position = 'absolute';
		var top = this.element.getStyle('top').toInt();
		var left = this.element.getStyle('left').toInt();
		if (this.position == 'absolute'){
			top = Moo.$chk(top) ? top : this.element.getTop();
			left = Moo.$chk(left) ? left : this.element.getLeft();
		} else {
			top = Moo.$chk(top) ? top : 0;
			left = Moo.$chk(left) ? left : 0;
		}
		this.element.setStyles({
			'top': top,
			'left': left,
			'position': this.position
		});
		this.parent(this.element, this.options);
	},

	start: function(event){
		this.container = Moo.$(this.options.container);
		if (this.container){
			var cont = this.container.getCoordinates();
			var el = this.element.getCoordinates();
			if (this.position == 'absolute'){
				this.options.limit = {
					'x': [cont.left, cont.right - el.width],
					'y': [cont.top, cont.bottom - el.height]
				};
			} else {
				var diffx = el.left - this.element.getStyle('left').toInt();
				var diffy = el.top - this.element.getStyle('top').toInt();
				this.options.limit = {
					'y': [-(diffy) + cont.top, cont.bottom - diffy - el.height],
					'x': [-(diffx) + cont.left, cont.right - diffx - el.width]
				};
			}
		}
		this.parent(event);
	},

	drag: function(event){
		this.parent(event);
		if (this.out) return this;
		this.droppables.each(function(drop){
			if (this.checkAgainst(Moo.$(drop))){
				if (!drop.overing) drop.fireEvent('over', [this.element, this]);
				drop.overing = true;
			} else {
				if (drop.overing) drop.fireEvent('leave', [this.element, this]);
				drop.overing = false;
			}
		}, this);
		return this;
	},

	checkAgainst: function(el){
		el = el.getCoordinates(this.options.overflown);
		return (this.mouse.now.x > el.left && this.mouse.now.x < el.right && this.mouse.now.y < el.bottom && this.mouse.now.y > el.top);
	},

	stop: function(){
		if (!this.out){
			var dropped = false;
			this.droppables.each(function(drop){
				if (this.checkAgainst(drop)){
					drop.fireEvent('drop', [this.element, this]);
					dropped = true;
				}
			}, this);
			if (!dropped) this.element.fireEvent('emptydrop', this);
		}
		this.parent();
		return this;
	}

});

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.extend({

	/*
	Property: makeDraggable
		Makes an element draggable with the supplied options.

	Arguments:
		options - see <Moo.Drag.Move> and <Moo.Drag.Base> for acceptable options.
	*/

	makeDraggable: function(options){
		return new Moo.Drag.Move(this, options);
	}

});

/*
Script: Moo.XHR.js
	Contains the basic XMLHttpRequest Moo.Class Wrapper.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.XHR
	Basic XMLHttpRequest Wrapper.

Arguments:
	options - an object with options names as keys. See options below.

Moo.Options:
	method - 'post' or 'get' - the protocol for the request; optional, defaults to 'post'.
	async - boolean: asynchronous option; true uses asynchronous requests. Defaults to true.
	onRequest - function to execute when the Moo.XHR request is fired.
	onSuccess - function to execute when the Moo.XHR request completes.
	onStateChange - function to execute when the state of the XMLHttpRequest changes.
	onFailure - function to execute when the state of the XMLHttpRequest changes.
	encoding - the encoding, defaults to utf-8.
	autoCancel - cancels the already running request if another one is sent. defaults to false.
	headers - accepts an object, that will be set to request headers.

Properties:
	running - true if the request is running.
	response - object, text and xml as keys. You can access this property in the onSuccess event.

Example:
	>var myXHR = new Moo.XHR({method: 'get'}).send('http://site.com/requestHandler.php', 'name=john&lastname=dorian');
*/

Moo.XHR = new Moo.Class({

	options: {
		method: 'post',
		async: true,
		onRequest: Moo.Class.empty,
		onSuccess: Moo.Class.empty,
		onFailure: Moo.Class.empty,
		urlEncoded: true,
		encoding: 'utf-8',
		autoCancel: false,
		headers: {}
	},

	initialize: function(options){
		this.transport = (window.XMLHttpRequest) ? new XMLHttpRequest() : (window.ie ? new ActiveXObject('Microsoft.XMLHTTP') : false);
		if (!this.transport) return;
		this.setOptions(options);
		this.options.isSuccess = this.options.isSuccess || this.isSuccess;
		this.headers = {};
		if (this.options.urlEncoded && this.options.method == 'post'){
			var encoding = (this.options.encoding) ? '; charset=' + this.options.encoding : '';
			this.setHeader('Content-type', 'application/x-www-form-urlencoded' + encoding);
		}
		if (this.options.initialize) this.options.initialize.call(this);
	},

	onStateChange: function(){
		if (this.transport.readyState != 4 || !this.running) return;
		this.running = false;
		var status = 0;
		try {status = this.transport.status} catch(e){};
		if (this.options.isSuccess.call(this, status)) this.onSuccess();
		else this.onFailure();
		this.transport.onreadystatechange = Moo.Class.empty;
	},

	isSuccess: function(status){
		return ((status >= 200) && (status < 300));
	},

	onSuccess: function(){
		this.response = {
			'text': this.transport.responseText,
			'xml': this.transport.responseXML
		};
		this.fireEvent('onSuccess', [this.response.text, this.response.xml]);
		this.callChain();
	},

	onFailure: function(){
		this.fireEvent('onFailure', this.transport);
	},

	/*
	Property: setHeader
		Add/modify an header for the request. It will not override headers from the options.

	Example:
		>var myAjax = new Moo.Ajax(url, {method: 'get', headers: {'X-Request': 'JSON'}});
		>myAjax.setHeader('Last-Modified','Sat, 1 Jan 2005 05:00:00 GMT');
	*/

	setHeader: function(name, value){
		this.headers[name] = value;
		return this;
	},

	/*
	Property: send
		Opens the xhr connection and sends the data. Data has to be null or a string.

	Example:
		>var myXhr = new Xhr({method: 'post'});
		>myXhr.send(url, querystring);
		>
		>var syncXhr = new Xhr({async: false, method: 'post'});
		>syncXhr.send(url, null);
		>
	*/

	send: function(url, data){
		if (this.options.autoCancel) this.cancel();
		else if (this.running) return this;
		this.running = true;
		if (data && this.options.method == 'get') url = url + (url.contains('?') ? '&' : '?') + data, data = null;
		(function(){
			this.transport.open(this.options.method, url, this.options.async);
			this.transport.onreadystatechange = this.onStateChange.bind(this);
			if ((this.options.method == 'post') && this.transport.overrideMimeType) this.setHeader('Connection', 'close');
			Moo.$extend(this.headers, this.options.headers);
			for (var type in this.headers) try {this.transport.setRequestHeader(type, this.headers[type]);} catch(e){};
			this.fireEvent('onRequest');
			this.transport.send(Moo.$pick(data, null));
		}).delay(this.options.async ? 1 : false, this);
		return this;
	},

	/*
	Property: cancel
		Cancels the running request. No effect if the request is not running.

	Example:
		>var myAjax = new Moo.Ajax(url, {method: 'get'}).request();
		>myAjax.cancel();
	*/

	cancel: function(){
		if (!this.running) return this;
		this.running = false;
		this.transport.abort();
		this.transport.onreadystatechange = Moo.Class.empty;
		this.fireEvent('onCancel');
		return this;
	}

});

Moo.XHR.implement(new Moo.Chain);
Moo.XHR.implement(new Moo.Events);
Moo.XHR.implement(new Moo.Options);

/*
Script: Moo.Ajax.js
	Contains the <Moo.Ajax> class. Also contains methods to generate querystings from forms and Objects.

Credits:
	Loosely based on the version from prototype.js <http://prototype.conio.net>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Ajax
	An Moo.Ajax class, For all your asynchronous needs. Inherits methods, properties and options from <Moo.XHR>.

Arguments:
	url - the url pointing to the server-side script.
	options - optional, an object containing options.

Moo.Options:
	data - you can write parameters here. Can be a querystring, an object or a Form element.
	onComplete - function to execute when the ajax request completes.
	update - Moo.$(element) to insert the response text of the Moo.XHR into, upon completion of the request.
	evalScripts - boolean; default is false. Execute scripts in the response text onComplete. When the response is javascript the whole response is evaluated.
	evalResponse - boolean; default is false. Force global evalulation of the whole response, no matter what content-type it is.

Example:
	>var myAjax = new Moo.Ajax(url, {method: 'get'}).request();
*/

Moo.Ajax = Moo.XHR.extend({

	options: {
		data: null,
		update: null,
		onComplete: Moo.Class.empty,
		evalScripts: false,
		evalResponse: false
	},

	initialize: function(url, options){
		this.addEvent('onSuccess', this.onComplete);
		this.setOptions(options);
		this.options.data = this.options.data || this.options.postBody;
		if (!['post', 'get'].contains(this.options.method)){
			this._method = '_method=' + this.options.method;
			this.options.method = 'post';
		}
		this.parent(this.options);
		this.setHeader('X-Requested-With', 'XMLHttpRequest');
		this.setHeader('Accept', 'text/javascript, text/html, application/xml, text/xml, */*');
		this.url = url;
	},

	onComplete: function(){
		if (this.options.update) Moo.$(this.options.update).setHTML(this.response.text);
		if (this.options.evalScripts || this.options.evalResponse) this.evalScripts();
		this.fireEvent('onComplete', [this.response.text, this.response.xml], 20);
	},

	/*
	Property: request
		Executes the ajax request.

	Example:
		>var myAjax = new Moo.Ajax(url, {method: 'get'});
		>myAjax.request();

		OR

		>new Moo.Ajax(url, {method: 'get'}).request();
	*/

	request: function(data){
		data = data || this.options.data;
		switch(Moo.$type(data)){
			case 'element': data = Moo.$(data).toQueryString(); break;
			case 'object': data = Object.toQueryString(data);
		}
		if (this._method) data = (data) ? [this._method, data].join('&') : this._method;
		return this.send(this.url, data);
	},

	/*
	Property: evalScripts
		Executes scripts in the response text
	*/

	evalScripts: function(){
		if (this.options.evalResponse || /(ecma|java)script/.test(this.getHeader('Content-type'))) var scripts = this.response.text;
		else {
			var script, scripts = [], regexp = /<script[^>]*>([\s\S]*?)<\/script>/gi;
			while ((script = regexp.exec(this.response.text))) scripts.push(script[1]);
			scripts = scripts.join('\n');
		}
		if (scripts) (window.execScript) ? window.execScript(scripts) : window.setTimeout(scripts, 0);
	},

	/*
	Property: getHeader
		Returns the given response header or null
	*/

	getHeader: function(name) {
		try {return this.transport.getResponseHeader(name);} catch(e){};
		return null;
	}

});

/* Section: Object related Functions */

/*
Function: Object.toQueryString
	Generates a querystring from key/pair values in an object

Arguments:
	source - the object to generate the querystring from.

Returns:
	the query string.

Example:
	>Object.toQueryString({apple: "red", lemon: "yellow"}); //returns "apple=red&lemon=yellow"
*/

Object.toQueryString = function(source){
	var queryString = [];
	for (var property in source) queryString.push(encodeURIComponent(property) + '=' + encodeURIComponent(source[property]));
	return queryString.join('&');
};

/*
Moo.Class: Moo.Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <Moo.$>.
*/

Moo.Element.extend({

	/*
	Property: send
		Sends a form with an ajax post request

	Arguments:
		options - option collection for ajax request. See <Moo.Ajax> for the options list.

	Returns:
		The Moo.Ajax Moo.Class Instance

	Example:
		(start code)
		<form id="myForm" action="submit.php">
		<input name="email" value="bob@bob.com">
		<input name="zipCode" value="90210">
		</form>
		<script>
		Moo.$('myForm').send()
		</script>
		(end)
	*/

	send: function(options){
		return new Moo.Ajax(this.getProperty('action'), Moo.$merge({postBody: this.toQueryString()}, options, {method: 'post'})).request();
	}

});

/*
Script: Moo.Cookie.js
	A cookie reader/creator

Credits:
	based on the functions by Peter-Paul Koch (http://quirksmode.org)
*/

/*
Moo.Class: Moo.Cookie
	Moo.Class for creating, getting, and removing cookies.
*/

Moo.Cookie = new Moo.Abstract({

	options: {
		domain: false,
		path: false,
		duration: false,
		secure: false
	},

	/*
	Property: set
		Sets a cookie in the browser.

	Arguments:
		key - the key (name) for the cookie
		value - the value to set, cannot contain semicolons
		options - an object representing the Moo.Cookie options. See Moo.Options below. Default values are stored in Moo.Cookie.options.

	Moo.Options:
		domain - the domain the Moo.Cookie belongs to. If you want to share the cookie with pages located on a different domain, you have to set this value. Defaults to the current domain.
		path - the path the Moo.Cookie belongs to. If you want to share the cookie with pages located in a different path, you have to set this value, for example to "/" to share the cookie with all pages on the domain. Defaults to the current path.
		duration - the duration of the Moo.Cookie before it expires, in seconds.
					If set to false or 0, the cookie will be a session cookie that expires when the browser is closed. This is default.
		secure - Stored cookie information can be accessed only from a secure environment.

	Example:
		>Moo.Cookie.set("username", "Harald", {duration: 3600}); //save this for 1 hour
		>Moo.Cookie.set("username", "JackBauer", {duration: false}); //session cookie

	*/

	set: function(key, value, options){
		options = Moo.$merge(this.options, options);
		value = encodeURIComponent(value);
		if (options.domain) value += '; domain=' + options.domain;
		if (options.path) value += '; path=' + options.path;
		if (options.duration){
			var date = new Date();
			date.setTime(date.getTime() + options.duration * 1000);
			value += '; expires=' + date.toGMTString();
		}
		if (options.secure) value += '; secure';
		document.cookie = key + '=' + value;
		return Moo.$extend(options, {'key': key, 'value': value});
	},

	/*
	Property: get
		Gets the value of a cookie.

	Arguments:
		key - the name of the cookie you wish to retrieve.

	Returns:
		The cookie string value, or false if not found.

	Example:
		>Moo.Cookie.get("username") //returns Aaron
	*/

	get: function(key){
		var value = document.cookie.match('(?:^|;)\\s*' + key.escapeRegExp() + '=([^;]*)');
		return value ? decodeURIComponent(value[1]) : false;
	},

	/*
	Property: remove
		Removes a cookie from the browser.

	Arguments:
		cookie - the name of the cookie to remove or a previous cookie (for domains)
		options - optional. you can also pass the domain and path here. Same as options in <Moo.Cookie.set>

	Examples:
		>Moo.Cookie.remove("username") //bye-bye Aaron
		>var myCookie = Moo.Cookie.set('user', 'jackbauer', {domain: 'mootools.net'});
		>Moo.Cookie.remove(myCookie);
	*/

	remove: function(cookie, options){
		if (Moo.$type(cookie) == 'object') this.set(cookie.key, '', Moo.$merge(cookie, {duration: -1}));
		else this.set(cookie, '', Moo.$merge(options, {duration: -1}));
	}

});

/*	
Script: Moo.Cookie.Json.js
	A cookie reader/creator that allows for the storage and retrieval of JSON data and collections of items.

Credits:
	based on the CookieJar class by Lalit Patel (http://www.lalit.org/lab/jsoncookies)
*/
	
/*
Moo.Class: Moo.Cookie.Json
	Stores an object as a cookie using json format.
	
Arguments:
	name - (string) a unique name for all the items in this jar; required.
	options - an object with options names as keys. See options below.

Moo.Options:
	These options are identical to <Moo.Cookie> and are simply passed along to it.
		
	domain - the domain the Moo.Cookie belongs to. If you want to share the cookie with pages located on a different domain, you have to set this value. Defaults to the current domain.
	path - the path the Moo.Cookie belongs to. If you want to share the cookie with pages located in a different path, you have to set this value, for example to "/" to share the cookie with all pages on the domain. Defaults to the current path.
	duration - the duration of the Moo.Cookie before it expires, in seconds. If set to false or 0, the cookie will be a session cookie that expires when the browser is closed. This is default.
	secure - Stored cookie information can be accessed only from a secure environment.

Example:
	(start code)
	var cookieJar = new Moo.Cookie.Json('myCookieName');
	var myObject = {
		lemon: 'yellow',
		apple: 'red'
	}
	cookieJar.put('myObject', myObject);
	cookieJar.get('lemon'); //returns yellow
	
	var myOtherObject = {
		apple: {
			sour: 'green',
			sweet: 'red'
		}
		lime: 'green',
		grape: 'purple'
	}
	cookieJar.merge(myOtherObject);
	cookieJar.get('apple').sour; //returns green
	(end)
*/

Moo.Cookie.Json = new Moo.Class({

	initialize: function(name, options){
		this.name = name;
		this.options = options;
		return;
	},

	/*	
	Property: set
		Puts an item into the collection (and stores it as a cookie with the name prefix).

	Arguments: 	
		key - (string) the name of the item
		value - (object) the object to store (can be a string, number, object, etc.)
	*/

	set: function(key, value){
		var object = this.get() || {};
		object[key] = value;
		this.save(object);
		return this;
	},
	
	/*	
	Property: save
		Saves an object for the entire value of this Moo.Json.Cookie, overwriting any previous data.

	Arguments: 	
		object - the value to save
	*/

	save: function(object){
		object = Moo.Json.toString(object);
		if (object.length > 4096) return false; //cookie would be truncated!
		Moo.Cookie.set(this.name, object, this.options);
		return this;
	},

	/*	
	Property: remove
		Removes an item from the collection.

	Arguments: 	
		key - (string) the name of the item
	*/

	remove: function(key){
		var object = this.get();
		delete object[key];
		this.save(object);
		return this;
	},

	/*	
	Property: get
		Returns the value of an item in the collection, or the entire collection.
	
	Arguments:
		key - (string) the name of the item; if not provided, the entire object is returned.
	*/

	get: function(key){
		var value = Moo.Cookie.get(this.name);
		if(value && !value.test(/^("(\\.|[^"\\\n\r])*?"|[,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t])+?$/)) {
			var object = Moo.Json.evaluate(value);
			return (key) ? object[key] : object;
		} else return false;
	},

	/*
	Property: empty
		Removes the cookie.
	*/

	empty: function(){
		this.save(null);
	},

	/*
	Property: merge
		Merges an object with the values in the cookie, *overwritting the cookie* values where namespaces overlap.
	*/
	
	merge: function(obj){
		this.save(Moo.$merge(this.get(), obj));
	},

	/*
	Property: fill
		Merges an object with the values in the cookie, *overwritting the passed in values* where namespaces overlap.
	*/
	
	fill: function(obj){
		this.save(Moo.$merge(obj, this.get()));
	}

});

/*
Script: Moo.Json.js
	Simple Moo.Json parser and Stringyfier, See: <http://www.json.org/>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Json
	Simple Moo.Json parser and Stringyfier, See: <http://www.json.org/>
*/

Moo.Json = {

	/*
	Property: toString
		Converts an object to a string, to be passed in server-side scripts as a parameter. Although its not normal usage for this class, this method can also be used to convert functions and arrays to strings.

	Arguments:
		obj - the object to convert to string

	Returns:
		A json string

	Example:
		(start code)
		Moo.Json.toString({apple: 'red', lemon: 'yellow'}); '{"apple":"red","lemon":"yellow"}'
		(end)
	*/

	toString: function(obj){
		switch(Moo.$type(obj)){
			case 'string':
				return '"' + obj.replace(/(["\\])/g, '\\Moo.$1') + '"';
			case 'array':
				return '[' + obj.map(function(ar){
					return Moo.Json.toString(ar);
				}).join(',') + ']';
			case 'object':
				var string = [];
				for (var property in obj) string.push(Moo.Json.toString(property) + ':' + Moo.Json.toString(obj[property]));
				return '{' + string.join(',') + '}';
		}
		return String(obj);
	},

	/*
	Property: evaluate
		converts a json string to an javascript Object.

	Arguments:
		str - the string to evaluate.

	Example:
		>var myObject = Moo.Json.evaluate('{"apple":"red","lemon":"yellow"}');
		>//myObject will become {apple: 'red', lemon: 'yellow'}
	*/

	evaluate: function(str){
		return eval('(' + str + ')');
	}

};

/*
Script: Moo.Json.Remote.js
	Contains <Moo.Json.Remote>.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Json.Remote
	Wrapped Moo.XHR with automated sending and receiving of Javascript Objects in Moo.Json Format.

Arguments:
	url - the url you want to send your object to.
	options - see <Moo.XHR> options

Example:
	this code will send user information based on name/last name
	(start code)
	var jSonRequest = new Moo.Json.Remote("http://site.com/tellMeAge.php", {onComplete: function(person){
		alert(person.age); //is 25 years
		alert(person.height); //is 170 cm
		alert(person.weight); //is 120 kg
	}}).send({'name': 'John', 'lastName': 'Doe'});
	(end)
*/

Moo.Json.Remote = Moo.XHR.extend({

	initialize: function(url, options){
		this.url = url;
		this.addEvent('onSuccess', this.onComplete);
		this.parent(options);
		this.setHeader('X-Request', 'JSON');
	},

	send: function(obj){
		return this.parent(this.url, 'json=' + Moo.Json.toString(obj));
	},

	onComplete: function(){
		this.fireEvent('onComplete', Moo.Json.evaluate(this.response.text));
	}

});

/*
Script: Assets.js
	provides dynamic loading for images, css and javascript files.

License:
	MIT-style license.
*/

Moo.Asset = new Moo.Abstract({

	/*
	Property: javascript
		Injects a javascript file in the page.

	Arguments:
		source - the path of the javascript file
		properties - some additional attributes you might want to add to the script element

	Example:
		> new Moo.Asset.javascript('/scripts/myScript.js', {id: 'myScript'});
	*/

	javascript: function(source, properties){
		properties = Moo.$merge({
			'onload': Moo.Class.empty
		}, properties);
		var script = new Moo.Element('script', {'src': source}).addEvents({
			'load': properties.onload,
			'readystatechange': function(){
				if (this.readyState == 'complete') this.fireEvent('load');
			}
		});
		delete properties.onload;
		return script.setProperties(properties).inject(document.head);
	},

	/*
	Property: css
		Injects a css file in the page.

	Arguments:
		source - the path of the css file
		properties - some additional attributes you might want to add to the link element

	Example:
		> new Moo.Asset.css('/css/myStyle.css', {id: 'myStyle', title: 'myStyle'});
	*/

	css: function(source, properties){
		return new Moo.Element('link', Moo.$merge({
			'rel': 'stylesheet', 'media': 'screen', 'type': 'text/css', 'href': source
		}, properties)).inject(document.head);
	},

	/*
	Property: image
		Preloads an image and returns the img element. does not inject it to the page.

	Arguments:
		source - the path of the image file
		properties - some additional attributes you might want to add to the img element

	Example:
		> new Moo.Asset.image('/images/myImage.png', {id: 'myImage', title: 'myImage', onload: myFunction});

	Returns:
		the img element. you can inject it anywhere you want with <Moo.Element.injectInside>/<Moo.Element.injectAfter>/<Moo.Element.injectBefore>
	*/

	image: function(source, properties){
		properties = Moo.$merge({
			'onload': Moo.Class.empty,
			'onabort': Moo.Class.empty,
			'onerror': Moo.Class.empty
		}, properties);
		var image = new Image();
		image.src = source;
		var element = new Moo.Element('img', {'src': source});
		['load', 'abort', 'error'].each(function(type){
			var event = properties['on' + type];
			delete properties['on' + type];
			element.addEvent(type, function(){
				this.removeEvent(type, arguments.callee);
				event.call(this);
			});
		});
		if (image.width && image.height) element.fireEvent('load');
		return element.setProperties(properties);
	},

	/*
	Property: images
		Preloads an array of images (as strings) and returns an array of img elements. does not inject them to the page.

	Arguments:
		sources - array, the paths of the image files
		options - object, see below

	Moo.Options:
		onComplete - a function to execute when all image files are loaded in the browser's cache
		onProgress - a function to execute when one image file is loaded in the browser's cache

	Example:
		(start code)
		new Moo.Asset.images(['/images/myImage.png', '/images/myImage2.gif'], {
			onComplete: function(){
				alert('all images loaded!');
			}
		});
		(end)

	Returns:
		the img elements as Moo.$$. you can inject them anywhere you want with <Moo.Element.injectInside>/<Moo.Element.injectAfter>/<Moo.Element.injectBefore>
	*/

	images: function(sources, options){
		options = Moo.$merge({
			onComplete: Moo.Class.empty,
			onProgress: Moo.Class.empty
		}, options);
		if (!sources.push) sources = [sources];
		var images = [];
		var counter = 0;
		sources.each(function(source){
			var img = new Moo.Asset.image(source, {
				'onload': function(){
					options.onProgress.call(this, counter);
					counter++;
					if (counter == sources.length) options.onComplete();
				}
			});
			images.push(img);
		});
		return Moo.$extend(images, new Moo.Elements);
	}

});

/*
Script: Moo.Hash.js
	Contains the class Moo.Hash.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Hash
	It wraps an object that it uses internally as a map. The user must use set(), get(), and remove() to add/change, retrieve and remove values, it must not access the internal object directly. null/undefined values are allowed.

Arguments:
	obj - an object to convert into a Moo.Hash instance.

Example:
	(start code)
	var hash = new Moo.Hash({a: 'hi', b: 'world', c: 'howdy'});
	hash.remove('b'); // b is removed.
	hash.set('c', 'hello');
	hash.get('c'); // returns 'hello'
	hash.length // returns 2 (a and c)
	(end)
*/

Moo.Hash = new Moo.Class({

	length: 0,

	initialize: function(obj){
		this.obj = {};
		this.extend(obj);
	},

	/*
	Property: get
		Retrieves a value from the hash.

	Arguments:
		key - The key

	Returns:
		The value
	*/

	get: function(key){
		return this.obj[key];
	},

	/*
	Property: hasKey
		Check the presence of a specified key-value pair in the hash.

	Arguments:
		key - The key

	Returns:
		True if the Moo.Hash contains a value for the specified key, otherwise false
	*/

	hasKey: function(key){
		return (key in this.obj);
	},

	/*
	Property: set
		Adds a key-value pair to the hash or replaces a previous value associated with the key.

	Arguments:
		key - The key
		value - The value
	*/

	set: function(key, value){
		if (key in this.obj) this.length++;
		this.obj[key] = value;
		return this;
	},

	/*
	Property: remove
		Removes a key-value pair from the hash.

	Arguments:
		key - The key
	*/

	remove: function(key){
		if (!(key in this.obj)) return this;
		delete this.obj[key];
		this.length--;
		return this;
	},

	/*
	Property: each
		Calls a function for each key-value pair. The first argument passed to the function will be the value, the second one will be the key, like Moo.$each.

	Arguments:
		fn - The function to call for each key-value pair
		bind - Optional, the object that will be referred to as "this" in the function
	*/

	each: function(fn, bind){
		Moo.$each(this.obj, fn, bind);
	},

	/*
	Property: extend
		Extends the current hash with an object containing key-value pairs. Values for duplicate keys will be replaced by the new ones.

	Arguments:
		obj - An object containing key-value pairs
	*/

	extend: function(obj){
		for (var key in obj) this.set(key, obj[key]);
		return this;
	},

	/*
	Property: empty
		Empties all hash values properties and values.
	*/

	empty: function(){
		this.obj = {};
		this.length = 0;
		return this;
	},

	/*
	Property: keys
		Returns an array containing all the keys, in the same order as the values returned by <Moo.Hash.values>.

	Returns:
		An array containing all the keys of the hash
	*/

	keys: function(){
		var keys = [];
		for (var property in this.obj) keys.push(property);
		return keys;
	},

	/*
	Property: values
		Returns an array containing all the values, in the same order as the keys returned by <Moo.Hash.keys>.

	Returns:
		An array containing all the values of the hash
	*/

	values: function(){
		var values = [];
		for (var property in this.obj) values.push(this.obj[property]);
		return values;
	}

});

/* Section: Utility Functions */

/*
Function: Moo.$H
	Shortcut to create a Moo.Hash from an Object.
*/

Moo.$H = function(obj){
	return new Moo.Hash(obj);
};

/*
Script: Moo.Color.js
	Contains the Moo.Color class.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Color
	Creates a new Moo.Color Object, which is an array with some color specific methods.
Arguments:
	color - the hex, the RGB array or the HSB array of the color to create. For HSB colors, you need to specify the second argument.
	type - a string representing the type of the color to create. needs to be specified if you intend to create the color with HSB values, or an array of HEX values. Can be 'rgb', 'hsb' or 'hex'.

Example:
	(start code)
	var black = new Moo.Color('#000');
	var purple = new Moo.Color([255,0,255]);
	// mix black with white and purple, each time at 10% of the new color
	var darkpurple = black.mix('#fff', purple, 10);
	Moo.$('myDiv').setStyle('background-color', darkpurple);
	(end)
*/

Moo.Color = new Moo.Class({

	initialize: function(color, type){
		type = type || (color.push ? 'rgb' : 'hex');
		var rgb, hsb;
		switch(type){
			case 'rgb':
				rgb = color;
				hsb = rgb.rgbToHsb();
				break;
			case 'hsb':
				rgb = color.hsbToRgb();
				hsb = color;
				break;
			default:
				rgb = color.hexToRgb(true);
				hsb = rgb.rgbToHsb();
		}
		rgb.hsb = hsb;
		rgb.hex = rgb.rgbToHex();
		return Moo.$extend(rgb, Moo.Color.prototype);
	},

	/*
	Property: mix
		Mixes two or more colors with the Moo.Color.
		
	Arguments:
		color - a color to mix. you can use as arguments how many colors as you want to mix with the original one.
		alpha - if you use a number as the last argument, it will be threated as the amount of the color to mix.
	*/

	mix: function(){
		var colors = Moo.$A(arguments);
		var alpha = (Moo.$type(colors[colors.length - 1]) == 'number') ? colors.pop() : 50;
		var rgb = this.copy();
		colors.each(function(color){
			color = new Moo.Color(color);
			for (var i = 0; i < 3; i++) rgb[i] = Math.round((rgb[i] / 100 * (100 - alpha)) + (color[i] / 100 * alpha));
		});
		return new Moo.Color(rgb, 'rgb');
	},

	/*
	Property: invert
		Inverts the Moo.Color.
	*/

	invert: function(){
		return new Moo.Color(this.map(function(value){
			return 255 - value;
		}));
	},

	/*
	Property: setHue
		Modifies the hue of the Moo.Color, and returns a new one.
	
	Arguments:
		value - the hue to set
	*/

	setHue: function(value){
		return new Moo.Color([value, this.hsb[1], this.hsb[2]], 'hsb');
	},

	/*
	Property: setSaturation
		Changes the saturation of the Moo.Color, and returns a new one.
	
	Arguments:
		percent - the percentage of the saturation to set
	*/

	setSaturation: function(percent){
		return new Moo.Color([this.hsb[0], percent, this.hsb[2]], 'hsb');
	},

	/*
	Property: setBrightness
		Changes the brightness of the Moo.Color, and returns a new one.
	
	Arguments:
		percent - the percentage of the brightness to set
	*/

	setBrightness: function(percent){
		return new Moo.Color([this.hsb[0], this.hsb[1], percent], 'hsb');
	}

});

/* Section: Utility Functions */

/*
Function: Moo.$RGB
	Shortcut to create a new color, based on red, green, blue values.

Arguments:
	r - (integer) red value (0-255)
	g - (integer) green value (0-255)
	b - (integer) blue value (0-255)

*/

Moo.$RGB = function(r, g, b){
	return new Moo.Color([r, g, b], 'rgb');
};

/*
Function: Moo.$HSB
	Shortcut to create a new color, based on hue, saturation, brightness values.

Arguments:
	h - (integer) hue value (0-100)
	s - (integer) saturation value (0-100)
	b - (integer) brightness value (0-100)
*/

Moo.$HSB = function(h, s, b){
	return new Moo.Color([h, s, b], 'hsb');
};

/*
Moo.Class: Array
	A collection of The Array Object prototype methods.
*/

Array.extend({
	
	/*
	Property: rgbToHsb
		Converts a RGB array to an HSB array.

	Returns:
		the HSB array.
	*/

	rgbToHsb: function(){
		var red = this[0], green = this[1], blue = this[2];
		var hue, saturation, brightness;
		var max = Math.max(red, green, blue), min = Math.min(red, green, blue);
		var delta = max - min;
		brightness = max / 255;
		saturation = (max != 0) ? delta / max : 0;
		if (saturation == 0){
			hue = 0;
		} else {
			var rr = (max - red) / delta;
			var gr = (max - green) / delta;
			var br = (max - blue) / delta;
			if (red == max) hue = br - gr;
			else if (green == max) hue = 2 + rr - br;
			else hue = 4 + gr - rr;
			hue /= 6;
			if (hue < 0) hue++;
		}
		return [Math.round(hue * 360), Math.round(saturation * 100), Math.round(brightness * 100)];
	},

	/*
	Property: hsbToRgb
		Converts an HSB array to an RGB array.

	Returns:
		the RGB array.
	*/

	hsbToRgb: function(){
		var br = Math.round(this[2] / 100 * 255);
		if (this[1] == 0){
			return [br, br, br];
		} else {
			var hue = this[0] % 360;
			var f = hue % 60;
			var p = Math.round((this[2] * (100 - this[1])) / 10000 * 255);
			var q = Math.round((this[2] * (6000 - this[1] * f)) / 600000 * 255);
			var t = Math.round((this[2] * (6000 - this[1] * (60 - f))) / 600000 * 255);
			switch(Math.floor(hue / 60)){
				case 0: return [br, t, p];
				case 1: return [q, br, p];
				case 2: return [p, br, t];
				case 3: return [p, q, br];
				case 4: return [t, p, br];
				case 5: return [br, p, q];
			}
		}
		return false;
	}

});

/*
Script: Moo.Scroller.js
	Contains the <Moo.Scroller>.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Scroller
	The Moo.Scroller is a class to scroll any element with an overflow (including the window) when the mouse cursor reaches certain buondaries of that element.
	You must call its start method to start listening to mouse movements.
	
Note:
	The Moo.Scroller requires an XHTML doctype.

Arguments:
	element - required, the element to scroll.
	options - optional, see options below, and <Moo.Fx.Base> options.

Moo.Options:
	area - integer, the necessary boundaries to make the element scroll.
	velocity - integer, velocity ratio, the modifier for the window scrolling speed.
	onChange - optionally, when the mouse reaches some boundaries, you can choose to alter some other values, instead of the scrolling offsets.
		Automatically passes as parameters x and y values.
*/

Moo.Scroller = new Moo.Class({

	options: {
		area: 20,
		velocity: 1,
		onChange: function(x, y){
			this.element.scrollTo(x, y);
		}
	},

	initialize: function(element, options){
		this.setOptions(options);
		this.element = Moo.$(element);
		this.mousemover = ([window, document].contains(element)) ? Moo.$(document.body) : this.element;
	},

	/*
	Property: start
		The scroller starts listening to mouse movements.
	*/

	start: function(){
		this.coord = this.getCoords.bindWithEvent(this);
		this.mousemover.addListener('mousemove', this.coord);
	},

	/*
	Property: stop
		The scroller stops listening to mouse movements.
	*/

	stop: function(){
		this.mousemover.removeListener('mousemove', this.coord);
		this.timer = Moo.$clear(this.timer);
	},

	getCoords: function(event){
		this.page = (this.element == window) ? event.client : event.page;
		if (!this.timer) this.timer = this.scroll.periodical(50, this);
	},

	scroll: function(){
		var el = this.element.getSize();
		var pos = this.element.getPosition();

		var change = {'x': 0, 'y': 0};
		for (var z in this.page){
			if (this.page[z] < (this.options.area + pos[z]) && el.scroll[z] != 0)
				change[z] = (this.page[z] - this.options.area - pos[z]) * this.options.velocity;
			else if (this.page[z] + this.options.area > (el.size[z] + pos[z]) && el.scroll[z] + el.size[z] != el.scrollSize[z])
				change[z] = (this.page[z] - el.size[z] + this.options.area - pos[z]) * this.options.velocity;
		}
		if (change.y || change.x) this.fireEvent('onChange', [el.scroll.x + change.x, el.scroll.y + change.y]);
	}

});

Moo.Scroller.implement(new Moo.Events);
Moo.Scroller.implement(new Moo.Options);

/*
Script: Moo.Slider.js
	Contains <Moo.Slider>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Slider
	Creates a slider with two elements: a knob and a container. Returns the values.
	
Note:
	The Moo.Slider requires an XHTML doctype.

Arguments:
	element - the knob container
	knob - the handle
	options - see Moo.Options below

Moo.Options:
	onChange - a function to fire when the value changes.
	onComplete - a function to fire when you're done dragging.
	onTick - optionally, you can alter the onTick behavior, for example displaying an effect of the knob moving to the desired position.
		Passes as parameter the new position.
	steps - the number of steps for your slider.
	mode - either 'horizontal' or 'vertical'. defaults to horizontal.
	offset - relative offset for knob position. default to 0.
*/

Moo.Slider = new Moo.Class({

	options: {
		onChange: Moo.Class.empty,
		onComplete: Moo.Class.empty,
		onTick: function(pos){
			this.knob.setStyle(this.p, pos);
		},
		mode: 'horizontal',
		steps: 100,
		offset: 0
	},

	initialize: function(el, knob, options){
		this.element = Moo.$(el);
		this.knob = Moo.$(knob);
		this.setOptions(options);
		this.previousChange = -1;
		this.previousEnd = -1;
		this.step = -1;
		this.element.addEvent('mousedown', this.clickedElement.bindWithEvent(this));
		var mod, offset;
		if (this.options.mode == 'horizontal'){
			this.z = 'x';
			this.p = 'left';
			mod = {'x': 'left', 'y': false};
			offset = 'offsetWidth';
		} else if (this.options.mode == 'vertical'){
			this.z = 'y';
			this.p = 'top';
			mod = {'x': false, 'y': 'top'};
			offset = 'offsetHeight';
		}
		this.max = this.element[offset] - this.knob[offset] + (this.options.offset * 2);
		this.half = this.knob[offset]/2;
		this.getPos = this.element['get' + this.p.capitalize()].bind(this.element);
		this.knob.setStyle('position', 'relative').setStyle(this.p, - this.options.offset);
		var lim = {};
		lim[this.z] = [- this.options.offset, this.max - this.options.offset];
		this.drag = new Moo.Drag.Base(this.knob, {
			limit: lim,
			modifiers: mod,
			snap: 0,
			onStart: function(){
				this.draggedKnob();
			}.bind(this),
			onDrag: function(){
				this.draggedKnob();
			}.bind(this),
			onComplete: function(){
				this.draggedKnob();
				this.end();
			}.bind(this)
		});
		if (this.options.initialize) this.options.initialize.call(this);
	},

	/*
	Property: set
		The slider will get the step you pass.

	Arguments:
		step - one integer
	*/

	set: function(step){
		if (step > this.options.steps) step = this.options.steps;
		else if (step < 0) step = 0;
		this.step = step;
		this.checkStep();
		this.end();
		this.fireEvent('onTick', this.toPosition(this.step));
		return this;
	},

	clickedElement: function(event){
		var position = event.page[this.z] - this.getPos() - this.half;
		if (position > this.max - this.options.offset) position = this.max - this.options.offset;
		else if (position < - this.options.offset) position = - this.options.offset;
		this.step = this.toStep(position);
		this.checkStep();
		this.end();
		this.fireEvent('onTick', position);
	},

	draggedKnob: function(){
		this.step = this.toStep(this.drag.value.now[this.z]);
		this.checkStep();
	},

	checkStep: function(){
		if (this.previousChange != this.step){
			this.previousChange = this.step;
			this.fireEvent('onChange', this.step);
		}
	},

	end: function(){
		if (this.previousEnd !== this.step){
			this.previousEnd = this.step;
			this.fireEvent('onComplete', this.step + '');
		}
	},

	toStep: function(position){
		return Math.round((position + this.options.offset) / this.max * this.options.steps);
	},

	toPosition: function(step){
		return (this.max) * step / this.options.steps;
	}

});

Moo.Slider.implement(new Moo.Events);
Moo.Slider.implement(new Moo.Options);

/*
Script: Moo.SmoothScroll.js
	Contains <Moo.SmoothScroll>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.SmoothScroll
	Auto targets all the anchors in a page and display a smooth scrolling effect upon clicking them.
	
Note:
	Moo.SmoothScroll requires an XHTML doctype.

Arguments:
	options - the Moo.Fx.Base options (see: <Moo.Fx.Base>) plus links, a collection of elements you want your smoothscroll on. Defaults to document.links.

Example:
	>new Moo.SmoothScroll();
*/

Moo.SmoothScroll = Moo.Fx.Scroll.extend({

	initialize: function(options){
		this.parent(window, options);
		this.links = (this.options.links) ? Moo.$$(this.options.links) : Moo.$$(document.links);
		this.addEvent('onCancel', this.clearChain);
		var location = window.location.href.match(/^[^#]*/)[0] + '#';
		this.links.each(function(link){
			if (link.href.indexOf(location) != 0) return;
			var anchor = link.href.substr(location.length);
			if (anchor && Moo.$(anchor)) this.useLink(link, anchor);
		}, this);
	},

	useLink: function(link, anchor){
		link.addEvent('click', function(event){
			if (!window.khtml){
				this.clearChain();
				this.chain(function(){
					window.location.hash = anchor;
				});
			}
			this.toElement(anchor);
			event.stop();
		}.bindWithEvent(this));
	}

});

/*
Script: Moo.Sortables.js
	Contains <Moo.Sortables> Moo.Class.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Sortables
	Creates an interface for <Moo.Drag.Base> and drop, resorting of a list.
	
Note:
	The Moo.Sortables require an XHTML doctype.

Arguments:
	list - required, the list that will become sortable.
	options - an Object, see options below.

Moo.Options:
	handles - a collection of elements to be used for drag handles. defaults to the elements.
	onStart - function executed when the item starts dragging
	onComplete - function executed when the item ends dragging
*/

Moo.Sortables = new Moo.Class({

	options: {
		handles: false,
		onStart: Moo.Class.empty,
		onComplete: Moo.Class.empty,
		ghost: true,
		snap: 3,
		onDragStart: function(element, ghost){
			ghost.setStyle('opacity', 0.7);
			element.setStyle('opacity', 0.7);
		},
		onDragComplete: function(element, ghost){
			element.setStyle('opacity', 1);
			ghost.remove();
			this.trash.remove();
		}
	},

	initialize: function(list, options){
		this.setOptions(options);
		this.list = Moo.$(list);
		this.elements = this.list.getChildren();
		this.handles = (this.options.handles) ? Moo.$$(this.options.handles) : this.elements;
		this.bound = {
			'start': [],
			'moveGhost': this.moveGhost.bindWithEvent(this)
		};
		for (var i = 0, l = this.handles.length; i < l; i++){
			this.bound.start[i] = this.start.bindWithEvent(this, this.elements[i]);
		}
		this.attach();
		if (this.options.initialize) this.options.initialize.call(this);
		this.bound.move = this.move.bindWithEvent(this);
		this.bound.end = this.end.bind(this);
	},
	
	attach: function(){
		this.handles.each(function(handle, i){
			handle.addEvent('mousedown', this.bound.start[i]);
		}, this);
	},

	detach: function(){
		this.handles.each(function(handle, i){
			handle.removeEvent('mousedown', this.bound.start[i]);
		}, this);
	},

	start: function(event, el){
		this.active = el;
		this.coordinates = this.list.getCoordinates();
		if (this.options.ghost){
			var position = el.getPosition();
			this.offset = event.page.y - position.y;
			this.trash = new Moo.Element('div').inject(document.body);
			this.ghost = el.clone().inject(this.trash).setStyles({
				'position': 'absolute',
				'left': position.x,
				'top': event.page.y - this.offset
			});
			document.addListener('mousemove', this.bound.moveGhost);
			this.fireEvent('onDragStart', [el, this.ghost]);
		}
		document.addListener('mousemove', this.bound.move);
		document.addListener('mouseup', this.bound.end);
		this.fireEvent('onStart', el);
		event.stop();
	},
	
	moveGhost: function(event){
		var value = event.page.y - this.offset;
		if (value < this.coordinates.top) value = this.coordinates.top;
		else if (value + this.ghost.offsetHeight > this.coordinates.bottom) value = this.coordinates.bottom - this.ghost.offsetHeight;
		this.ghost.setStyle('top', value);
		event.stop();
	},

	move: function(event){
		this.active.active = true;
		this.previous = this.previous || event.page.y;
		this.now = event.page.y;
		var direction = ((this.previous - this.now) <= 0) ? 'down' : 'up';
		var prev = this.active.getPrevious();
		var next = this.active.getNext();
		if (prev && direction == 'up'){
			var prevPos = prev.getCoordinates();
			if (event.page.y < prevPos.bottom) this.active.injectBefore(prev);
		}
		if (next && direction == 'down'){
			var nextPos = next.getCoordinates();
			if (event.page.y > nextPos.top) this.active.injectAfter(next);
		}
		this.previous = event.page.y;
	},

	serialize: function(){
		var serial = [];
		this.list.getChildren().each(function(el, i){
			serial[i] = this.elements.indexOf(el);
		}, this);
		return serial;
	},

	end: function(){
		this.previous = null;
		document.removeListener('mousemove', this.bound.move);
		document.removeListener('mouseup', this.bound.end);
		if (this.options.ghost){
			document.removeListener('mousemove', this.bound.moveGhost);
			this.fireEvent('onDragComplete', [this.active, this.ghost]);
		}
		this.fireEvent('onComplete', this.active);
	}

});

Moo.Sortables.implement(new Moo.Events);
Moo.Sortables.implement(new Moo.Options);

/*
Script: Moo.Tips.js
	Tooltips, BubbleTips, whatever they are, they will appear on mouseover

License:
	MIT-style license.

Credits:
	The idea behind Moo.Tips.js is based on Bubble Tooltips (<http://web-graphics.com/mtarchive/001717.php>) by Alessandro Fulcitiniti <http://web-graphics.com>
*/

/*
Moo.Class: Moo.Tips
	Display a tip on any element with a title and/or href.
	
Note:
	Moo.Tips requires an XHTML doctype.

Arguments:
	elements - a collection of elements to apply the tooltips to on mouseover.
	options - an object. See options Below.

Moo.Options:
	maxTitleChars - the maximum number of characters to display in the title of the tip. defaults to 30.

	onShow - optionally you can alter the default onShow behaviour with this option (like displaying a fade in effect);
	onHide - optionally you can alter the default onHide behaviour with this option (like displaying a fade out effect);

	showDelay - the delay the onShow method is called. (defaults to 100 ms)
	hideDelay - the delay the onHide method is called. (defaults to 100 ms)

	className - the prefix for your tooltip classNames. defaults to 'tool'.
	
		the whole tooltip will have as classname: tool-tip
		
		the title will have as classname: tool-title
		
		the text will have as classname: tool-text

	offsets - the distance of your tooltip from the mouse. an Object with x/y properties.
	fixed - if set to true, the toolTip will not follow the mouse.

Example:
	(start code)
	<img src="/images/i.png" title="The body of the tooltip is stored in the title" class="toolTipImg"/>
	<script>
		var myTips = new Moo.Tips(Moo.$$('.toolTipImg'), {
			maxTitleChars: 50	//I like my captions a little long
		});
	</script>
	(end)

Note:
	The title of the element will always be used as the tooltip body. If you put :: on your title, the text before :: will become the tooltip title.
*/

Moo.Tips = new Moo.Class({

	options: {
		onShow: function(tip){
			tip.setStyle('visibility', 'visible');
		},
		onHide: function(tip){
			tip.setStyle('visibility', 'hidden');
		},
		maxTitleChars: 30,
		showDelay: 100,
		hideDelay: 100,
		className: 'tool',
		offsets: {'x': 16, 'y': 16},
		fixed: false
	},

	initialize: function(elements, options){
		this.setOptions(options);
		this.toolTip = new Moo.Element('div', {
			'class': this.options.className + '-tip',
			'styles': {
				'position': 'absolute',
				'top': '0',
				'left': '0',
				'visibility': 'hidden'
			}
		}).inject(document.body);
		this.wrapper = new Moo.Element('div').inject(this.toolTip);
		Moo.$each(elements, function(el){
			this.build(Moo.$(el));
		}, this);
		if (this.options.initialize) this.options.initialize.call(this);
	},

	build: function(el){
		el.$.myTitle = (el.href && el.getTag() == 'a') ? el.href.replace('http://', '') : (el.rel || false);
		if (el.title){
			var dual = el.title.split('::');
			if (dual.length > 1) {
				el.$.myTitle = dual[0].trim();
				el.$.myText = dual[1].trim();
			} else {
				el.$.myText = el.title;
			}
			el.removeAttribute('title');
		} else {
			el.$.myText = false;
		}
		if (el.$.myTitle && el.$.myTitle.length > this.options.maxTitleChars) el.$.myTitle = el.$.myTitle.substr(0, this.options.maxTitleChars - 1) + "&hellip;";
		el.addEvent('mouseenter', function(event){
			this.start(el);
			if (!this.options.fixed) this.locate(event);
			else this.position(el);
		}.bind(this));
		if (!this.options.fixed) el.addEvent('mousemove', this.locate.bindWithEvent(this));
		el.addEvent('mouseleave', this.end.bind(this));
	},

	start: function(el){
		this.wrapper.empty();
		if (el.$.myTitle){
			this.title = new Moo.Element('span').inject(
				new Moo.Element('div', {'class': this.options.className + '-title'}).inject(this.wrapper)
			).setHTML(el.$.myTitle);
		}
		if (el.$.myText){
			this.text = new Moo.Element('span').inject(
				new Moo.Element('div', {'class': this.options.className + '-text'}).inject(this.wrapper)
			).setHTML(el.$.myText);
		}
		Moo.$clear(this.timer);
		this.timer = this.show.delay(this.options.showDelay, this);
	},

	end: function(event){
		Moo.$clear(this.timer);
		this.timer = this.hide.delay(this.options.hideDelay, this);
	},

	position: function(element){
		var pos = element.getPosition();
		this.toolTip.setStyles({
			'left': pos.x + this.options.offsets.x,
			'top': pos.y + this.options.offsets.y
		});
	},

	locate: function(event){
		var win = {'x': window.getWidth(), 'y': window.getHeight()};
		var scroll = {'x': window.getScrollLeft(), 'y': window.getScrollTop()};
		var tip = {'x': this.toolTip.offsetWidth, 'y': this.toolTip.offsetHeight};
		var prop = {'x': 'left', 'y': 'top'};
		for (var z in prop){
			var pos = event.page[z] + this.options.offsets[z];
			if ((pos + tip[z] - scroll[z]) > win[z]) pos = event.page[z] - this.options.offsets[z] - tip[z];
			this.toolTip.setStyle(prop[z], pos);
		};
	},

	show: function(){
		this.fireEvent('onShow', [this.toolTip]);
	},

	hide: function(){
		this.fireEvent('onHide', [this.toolTip]);
	}

});

Moo.Tips.implement(new Moo.Events);
Moo.Tips.implement(new Moo.Options);

/*
Script: Moo.Group.js
	For Grouping Classes or Moo.Elements Moo.Events. The Moo.Event added to the Moo.Group will fire when all of the events of the items of the group are fired.

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Group
	An "Utility" Moo.Class.
*/

Moo.Group = new Moo.Class({

	initialize: function(){
		this.instances = Moo.$A(arguments);
		this.events = {};
		this.checker = {};
	},

	addEvent: function(type, fn){
		this.checker[type] = this.checker[type] || {};
		this.events[type] = this.events[type] || [];
		if (this.events[type].contains(fn)) return false;
		else this.events[type].push(fn);
		this.instances.each(function(instance, i){
			instance.addEvent(type, this.check.bind(this, [type, instance, i]));
		}, this);
		return this;
	},

	check: function(type, instance, i){
		this.checker[type][i] = true;
		var every = this.instances.every(function(current, j){
			return this.checker[type][j] || false;
		}, this);
		if (!every) return;
		this.instances.each(function(current, j){
			this.checker[type][j] = false;
		}, this);
		this.events[type].each(function(event){
			event.call(this, this.instances, instance);
		}, this);
	}

});

/*
Script: Moo.Accordion.js
	Contains <Moo.Accordion>

License:
	MIT-style license.
*/

/*
Moo.Class: Moo.Accordion
	The Moo.Accordion class creates a group of elements that are toggled when their handles are clicked. When one elements toggles in, the others toggles back.
	
Note:
	The Moo.Accordion requires an XHTML doctype.

Arguments:
	togglers - required, a collection of elements, the elements handlers that will be clickable.
	elements - required, a collection of elements the transitions will be applied to.
	options - optional, see options below, and <Moo.Fx.Base> options.

Moo.Options:
	show - integer, the Index of the element to show at start.
	display - integer, the Index of the element to show at start (with a transition). defaults to 0.
	fixedHeight - integer, if you want the elements to have a fixed height. defaults to false.
	fixedWidth - integer, if you want the elements to have a fixed width. defaults to false.
	onActive - function to execute when an element starts to show
	onBackground - function to execute when an element starts to hide
	height - boolean, will add a height transition to the accordion if true. defaults to true.
	opacity - boolean, will add an opacity transition to the accordion if true. defaults to true.
	width - boolean, will add a width transition to the accordion if true. defaults to false, css mastery is required to make this work!
	alwaysHide - boolean, will allow to hide all elements if true, instead of always keeping one element shown. defaults to false.
*/

Moo.Accordion = Moo.Fx.Elements.extend({

	options: {
		onActive: Moo.Class.empty,
		onBackground: Moo.Class.empty,
		display: 0,
		show: false,
		height: true,
		width: false,
		opacity: true,
		fixedHeight: false,
		fixedWidth: false,
		wait: false,
		alwaysHide: false
	},

	initialize: function(){
		var options, togglers, elements, container;
		Moo.$each(arguments, function(argument, i){
			switch(Moo.$type(argument)){
				case 'object': options = argument; break;
				case 'element': container = Moo.$(argument); break;
				default:
					var temp = Moo.$$(argument);
					if (!togglers) togglers = temp;
					else elements = temp;
			}
		});
		this.togglers = togglers || [];
		this.elements = elements || [];
		this.container = Moo.$(container);
		this.setOptions(options);
		this.previous = -1;
		if (this.options.alwaysHide) this.options.wait = true;
		if (Moo.$chk(this.options.show)){
			this.options.display = false;
			this.previous = this.options.show;
		}
		if (this.options.start){
			this.options.display = false;
			this.options.show = false;
		}
		this.effects = {};
		if (this.options.opacity) this.effects.opacity = 'fullOpacity';
		if (this.options.width) this.effects.width = this.options.fixedWidth ? 'fullWidth' : 'offsetWidth';
		if (this.options.height) this.effects.height = this.options.fixedHeight ? 'fullHeight' : 'scrollHeight';
		for (var i = 0, l = this.togglers.length; i < l; i++) this.addSection(this.togglers[i], this.elements[i]);
		this.elements.each(function(el, i){
			if (this.options.show === i) this.fireEvent('onActive', [this.togglers[i], el]);
			else for (var fx in this.effects) el.setStyle(fx, 0);
		}, this);
		this.parent(this.elements, this.options);
		if (Moo.$chk(this.options.display)) this.display(this.options.display);
	},

	addSection: function(toggler, element, pos){
		toggler = Moo.$(toggler);
		element = Moo.$(element);
		var test = this.togglers.contains(toggler);
		var len = this.togglers.length;
		this.togglers.include(toggler);
		this.elements.include(element);
		if (len && (!test || pos)){
			pos = Moo.$pick(pos, len - 1);
			toggler.injectBefore(this.togglers[pos]);
			element.injectAfter(toggler);
		} else if (this.container && !test){
			toggler.inject(this.container);
			element.inject(this.container);
		}
		var idx = this.togglers.indexOf(toggler);
		toggler.addEvent('click', this.display.bind(this, idx));
		if (this.options.height) element.setStyles({'padding-top': 0, 'border-top': 'none', 'padding-bottom': 0, 'border-bottom': 'none'});
		if (this.options.width) element.setStyles({'padding-left': 0, 'border-left': 'none', 'padding-right': 0, 'border-right': 'none'});
		element.fullOpacity = 1;
		if (this.options.fixedWidth) element.fullWidth = this.options.fixedWidth;
		if (this.options.fixedHeight) element.fullHeight = this.options.fixedHeight;
		element.setStyle('overflow', 'hidden');
		if (!test) for (var fx in this.effects) element.setStyle(fx, 0);
		return this;
	},

	/*
	Property: display
		Shows a specific section and hides all others. Useful when triggering an accordion from outside.

	Arguments:
		index - integer, the index of the item to show, or the actual element to show.
	*/

	display: function(index){
		index = (Moo.$type(index) == 'element') ? this.elements.indexOf(index) : index;
		if ((this.timer && this.options.wait) || (index === this.previous && !this.options.alwaysHide)) return this;
		this.previous = index;
		var obj = {};
		this.elements.each(function(el, i){
			obj[i] = {};
			if ((i != index) || (this.options.alwaysHide && (el.offsetHeight > 0))){
				this.fireEvent('onBackground', [this.togglers[i], el]);
				for (var fx in this.effects) obj[i][fx] = 0;
			} else {
				this.fireEvent('onActive', [this.togglers[i], el]);
				for (var fx in this.effects) obj[i][fx] = el[this.effects[fx]];
			}
		}, this);
		return this.start(obj);
	},

	showThisHideOpen: function(index){return this.display(index)}

});

Moo.Fx.Accordion = Moo.Accordion;