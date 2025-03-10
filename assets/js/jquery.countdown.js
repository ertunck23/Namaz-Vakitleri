/*!
 * jQuery Countdown plugin v1.0
 * http://www.littlewebthings.com/projects/countdown/
 *
 * Copyright 2010, Vassilis Dourdounis
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
(function(jQuery){

	jQuery.fn.countDown = function (options) {

		config = {};

		jQuery.extend(config, options);

		diffSecs = this.setCountDown(config);
	
		if (config.onComplete)
		{
			jQuery.data(jQuery(this)[0], 'callback', config.onComplete);
		}
		if (config.omitWeeks)
		{
			jQuery.data(jQuery(this)[0], 'omitWeeks', config.omitWeeks);
		}

		jQuery('#' + jQuery(this).attr('id') + ' .digit').html('<div class="top"></div><div class="bottom"></div>');
		jQuery(this).doCountDown(jQuery(this).attr('id'), diffSecs, 500);

		return this;

	};

	jQuery.fn.stopCountDown = function () {
		clearTimeout(jQuery.data(this[0], 'timer'));
	};

	jQuery.fn.startCountDown = function () {
		this.doCountDown(jQuery(this).attr('id'),jQuery.data(this[0], 'diffSecs'), 500);
	};

	jQuery.fn.setCountDown = function (options) {
		var targetTime = new Date();

		if (options.targetDate)
		{
			targetTime = new Date(options.targetDate.month + '/' + options.targetDate.day + '/' + options.targetDate.year + ' ' + options.targetDate.hour + ':' + options.targetDate.min + ':' + options.targetDate.sec + (options.targetDate.utc ? ' UTC' : ''));
		}
		else if (options.targetOffset)
		{
			targetTime.setFullYear(options.targetOffset.year + targetTime.getFullYear());
			targetTime.setMonth(options.targetOffset.month + targetTime.getMonth());
			targetTime.setDate(options.targetOffset.day + targetTime.getDate());
			targetTime.setHours(options.targetOffset.hour + targetTime.getHours());
			targetTime.setMinutes(options.targetOffset.min + targetTime.getMinutes());
			targetTime.setSeconds(options.targetOffset.sec + targetTime.getSeconds());
		}

		var nowTime = new Date();

		diffSecs = Math.floor((targetTime.valueOf()-nowTime.valueOf())/1000);

		jQuery.data(this[0], 'diffSecs', diffSecs);

		return diffSecs;
	};

	jQuery.fn.doCountDown = function (id, diffSecs, duration) {
		jQuerythis = jQuery('#' + id);
		if (diffSecs <= 0)
		{
			diffSecs = 0;
			if (jQuery.data(jQuerythis[0], 'timer'))
			{
				clearTimeout(jQuery.data(jQuerythis[0], 'timer'));
			}
		}

		secs = diffSecs % 60;
		mins = Math.floor(diffSecs/60)%60;
		hours = Math.floor(diffSecs/60/60)%24;
		if (jQuery.data(jQuerythis[0], 'omitWeeks') == true)
		{
			days = Math.floor(diffSecs/60/60/24);
			weeks = Math.floor(diffSecs/60/60/24/7);
		}
		else 
		{
			days = Math.floor(diffSecs/60/60/24)%7;
			weeks = Math.floor(diffSecs/60/60/24/7);
		}

		jQuerythis.dashChangeTo(id, 'seconds_dash', secs, duration ? duration : 800);
		jQuerythis.dashChangeTo(id, 'minutes_dash', mins, duration ? duration : 1200);
		jQuerythis.dashChangeTo(id, 'hours_dash', hours, duration ? duration : 1200);
		jQuerythis.dashChangeTo(id, 'days_dash', days, duration ? duration : 1200);
		jQuerythis.dashChangeTo(id, 'weeks_dash', weeks, duration ? duration : 1200);

		jQuery.data(jQuerythis[0], 'diffSecs', diffSecs);
		if (diffSecs > 0)
		{
			e = jQuerythis;
			t = setTimeout(function() { e.doCountDown(id, diffSecs-1) } , 1000);
			jQuery.data(e[0], 'timer', t);
		} 
		else if (cb = jQuery.data(jQuerythis[0], 'callback')) 
		{
			jQuery.data(jQuerythis[0], 'callback')();
		}

	};

	jQuery.fn.dashChangeTo = function(id, dash, n, duration) {
		  jQuerythis = jQuery('#' + id);
		 
		  for (var i=(jQuerythis.find('.' + dash + ' .digit').length-1); i>=0; i--)
		  {
				var d = n%10;
				n = (n - d) / 10;
				jQuerythis.digitChangeTo('#' + jQuerythis.attr('id') + ' .' + dash + ' .digit:eq('+i+')', d, duration);
		  }
	};

	jQuery.fn.digitChangeTo = function (digit, n, duration) {
		if (!duration)
		{
			duration = 800;
		}
		if (jQuery(digit + ' div.top').html() != n + '')
		{

			jQuery(digit + ' div.top').css({'display': 'none'});
			jQuery(digit + ' div.top').html((n ? n : '0')).slideDown(duration);

			jQuery(digit + ' div.bottom').animate({'height': ''}, duration, function() {
				jQuery(digit + ' div.bottom').html(jQuery(digit + ' div.top').html());
				jQuery(digit + ' div.bottom').css({'display': 'block', 'height': ''});
				jQuery(digit + ' div.top').hide().slideUp(10);

			
			});
		}
	};

})(jQuery);


