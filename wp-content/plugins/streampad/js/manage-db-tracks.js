/*

	Streampad

Copyright 2009 AOL, LLC  (email : gregory.tomlinson@corp.aol.com | http://gregorytomlinson.com/encoded/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


var SPDBManager = {
	
	bxNme : 'trackList',
	eBxNme : 'exisitingTrackList',
	start : 0,
	totalTracks : 0,
	count: 10,
	adminurl : '',
	defaultMp3Url : 'http://mydomain.com/sweet_track.mp3',
	defaultTitle : 'Track Title',
	
	renderUpdateButtons : function( key ) {
		var frme = jQuery(" <span />" ).attr('key', key).addClass("sp_update_cntrl_bx"), self=this;
		var u = jQuery(" <a />" ).html("Update").attr( "href", "#")
				.appendTo( frme )
				.click( function(e) {
					self.updateTrack(e);
					return false;
				} );
		var p = jQuery(" <span />" ).html(" | ").appendTo( frme );		
		var d = jQuery(" <a />" ).html("Delete").attr( "href", "#")
		         .appendTo( frme )
		         .click( function(e) {
						self.deleteTrack(e);
						return false;
					});
		
		return frme;
	},
		
	renderEditableItems : function( title, url, unique_id, startColor ) {
		var self = this, frme = jQuery(" <div />" )
								.attr("key", unique_id || ''); // this only gets added when the item already exists
		
		var input = jQuery(" <input />" )
					.attr( "value", title || this.defaultTitle).attr( "name", "title").css("color", startColor || "#999999")
					.addClass( "regular-text" )
					.focus( function(e) {
						var v = e.target.value;
						if( v == self.defaultTitle ) { e.target.value =''; e.target.style.color='#000000'; }
					})
					.blur( function(e) {
						var v = e.target.value;					
						if( v == '' ) { e.target.value = self.defaultTitle; e.target.style.color='#999999'; }
					})										
					.appendTo( frme );
		var enclosure = jQuery(" <input />" )
					.attr("name", "enclosure").attr("value", url || this.defaultMp3Url).css("color", startColor || "#999999")
					.addClass( "regular-text code" )
					.focus( function(e) {
						var v = e.target.value;
						if( v == self.defaultMp3Url ) { e.target.value =''; e.target.style.color='#000000'; }
					})
					.blur( function(e) {
						var v = e.target.value;					
						if( v == '' ) { e.target.value = self.defaultMp3Url; e.target.style.color='#999999'; }
					})
					.appendTo( frme );	
													
		return frme;	
	},
	
	renderNextPagination : function() {
		var self = this;
		
		var next = jQuery(" <a />").html("Next >>")
		.attr( "href", "#")
		.click( function(e) {
			self.fetch( self.start + self.count );
			return false;
		});			
		return next;
	},
	
	renderPrevPagination : function() {
		var self = this;
		
		var prev = jQuery(" <a />").html("<< Previous")
		.attr( "href", "#")
		.click( function(e) {
			var s = (self.start - self.count >= 0) ? self.start - self.count : 0;
			self.fetch( s );
			return false;
		});			
		return prev;	
	},
	
	validateTrackInfo : function( parent ) {
		var cs = jQuery( parent ).children('input'), data = {}, errors=[], data={}, i, self=this;
		for( i=0;i<cs.length;i++ ) {
			switch (cs[i].name) {
				
				case 'title' : 
					if( cs[i].value === this.defaultTitle || cs[i].value === '' ) {
						errors.push( "Please use a 'real' title" );
					} else {
						data.title = cs[i].value;
						cs[i].style.color="#999999";
						//cs[i].disabled=true;						
					}
				break;
				
				case 'enclosure' :
					if( cs[i].value === this.defaultMp3Url || cs[i].value === '' ) {
						errors.push( "Please use a valid MP3 URL" );					
					} else {
						data.enclosure = cs[i].value;
						cs[i].style.color="#999999";						
						//cs[i].disabled=true;
					}
				break;
				
				default :
					errors.push('Unknown error!');
				break;
			}
		}
		
		if( errors.length > 0 ) { return  { 'errors' : errors }; }
		else {
			return data;
		}
	},
	
	/////////////////////////////////////////////////////////////////
	// EVENTS
	/////////////////////////////////////////////////////////////////	
	addNewTrack : function( e ) {
		var a=jQuery(e.target), p = a.parent().get(0), self=this, data = this.validateTrackInfo( p );
		
		if( data.errors !== undefined ) {
			alert("Oops, you have to use real data!"); 
			return;
		}		
		
		
		 // remove the save button

		// connect to server!
		jQuery.post( this.adminurl, {
			action : 'save_single_track',
			title : encodeURIComponent(data.title),
			enclosure : encodeURIComponent(data.enclosure),
			'cookie' : encodeURIComponent(document.cookie)	
		}, function( response ) {
			//self.log( response );
			if(response === '-1' || response === -1) {
				alert("You're not logged in!");
			} else if( response === '0' || response === 0 ) {
				alert("Darn, that didn't work. Don't cry though.");
			} 
			else {
				a.remove();
				jQuery(p).remove();
				self.fetch(0);
			}
		});
		
		
		return false;
		// empty the div out and say 'saved' if it works!
	},
	
	deleteTrack : function( e ) {		
		var delBttn=jQuery( e.target ), span = delBttn.parent().get(0), sp_id = span.getAttribute('key'), p = jQuery(span).parent().get(0), self=this;
		jQuery.post( this.adminurl, {
			action : 'delete_single_track',
			unique_id : sp_id,
			'cookie' : encodeURIComponent(document.cookie)	
		}, function( response ) {
			//self.log( response );
			// TODO : GET A REAL RESPONSE BACK!!!!
			if(response === '-1' || response === -1) {
				alert("You're not logged in!");
			} else if( response === '0' || response === 0 ) {
				alert("Darn, that didn't work. Don't cry though.");
			} 
			else {
				jQuery(p).remove();
			}
		});		
	},
	
	updateTrack : function( e ) {

		var delBttn=jQuery( e.target ), span = delBttn.parent().get(0), 
		     sp_id = span.getAttribute('key'), p = jQuery(span).parent().get(0), 
		     data = this.validateTrackInfo( p ), self=this;
		
		if( data.errors !== undefined ) {
			alert("Oops, You can't leave fields blank!"); 
			return;
		}		
		
		jQuery.post( this.adminurl, {
			action : 'update_single_track',
			unique_id : sp_id,
			title : encodeURIComponent(data.title),
			enclosure : encodeURIComponent(data.enclosure),
			'cookie' : encodeURIComponent(document.cookie)	
		}, function( response ) {
			//self.log( response );
			if(response === '-1' || response === -1) {
				alert("You're not logged in!");
			} else if( response === '0' || response === 0 ) {
				// TODO: this responds 0 if the values didn't change... that's fine - just deal with it
				alert("Darn, that didn't work. Are you sure you changed it?.");
			} 
			else {
				//jQuery(p).remove();
			}
		});		
		
	},
	
	displayInsert : function() {
		var bx = jQuery( '#' + this.bxNme ), self=this, itm = this.renderEditableItems('','','');
		var aU = jQuery(" <a />" )
             		.addClass( "add_sp_tracks" )
					.attr( "href", "#")
					.html("Save")
					.click( function(e) {
						self.addNewTrack(e);
						return false;
					} )
					.appendTo( itm );			
		itm.appendTo( bx );
	},
	
	// Publicly Used Hooks - Don't change the names!
	display : function( jsonObj ) {
		var t = jsonObj.tracks, bx = jQuery( '#' + this.eBxNme );
		this.totalTracks = jsonObj.totalcount;
		bx.empty();
		for(var i=0; i<t.length;i++) {
			var itm = this.renderEditableItems( t[i].title, t[i].enclosure, t[i].id, "#000000" );
			this.renderUpdateButtons( t[i].id ).appendTo( itm );
			itm.appendTo( bx );
		}
		
		var frme = jQuery(" <div />").addClass("sp_pagination_cntrl_bx");
		
		if( this.start > 0 ) {
			this.renderPrevPagination().appendTo( frme );
			jQuery(" <span />").html(" | ").appendTo( frme );			
		}

		if( this.start + this.count < this.totalTracks ) {
			this.renderNextPagination().appendTo( frme );
		}
		
		frme.appendTo(bx);
		// add some pagination here
	},	
	
	// Remote Connection
	fetch : function( start ) {
		var self = this;
		self.start = start
		jQuery.ajax({
		  dataType: 'jsonp',
		  data: 'streampadapi=true&start='+ start +'&count=' + self.count,
		  jsonp: 'callback',
		  url: '?admin=true',
		  success: function ( jsonObj ) {
			self.display( jsonObj );
		  }
		});
	},
	
	log : function( str ) {
		try {
			console.log( str );
		} catch(e){}
	}
};

/* Init the Buttons for Action */
jQuery(document).ready( function($) {

	var a = jQuery('#showTracks'), ins = jQuery('#insertTracks');
	SPDBManager.adminurl = ins[0].getAttribute('href'); // for some reason getting an ID returns an array.. jquery is quirky!
	// add the event to the button
	a.click( function( e ) {
		SPDBManager.fetch( 0 );
		return false;		
	});
	
	ins.click( function(e) {
		SPDBManager.displayInsert( e );
		return false;			
	} );

})


