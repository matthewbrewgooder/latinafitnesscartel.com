jQuery( function( $ ) {
  mediaControl = {
    // Initializes a new media manager or returns an existing frame.
    // @see wp.media.featuredImage.frame()
    selector: null,
    size: null,
    container: null,
    frame: function() {
      if ( this._frame )
        return this._frame;

      this._frame = wp.media( {
        frame: 'post',
        title: 'Image',
        library: {
          type: 'image'
        },
        button: {
          text: 'Update'
        },
        multiple: false
      } );

      this._frame.on( 'insert', this.select );

      return this._frame;
    },

    select: function( selection ) {
      // Do something when the "update" button is clicked after a selection is
      // made.

      var selector = $( '.tyche-media-control' ).find( mediaControl.selector );
      var attachments = selection.toJSON();
      var url;

      if ( undefined !== attachments[0]['sizes']['medium'] ) {
        url = attachments[0]['sizes']['medium']['url'];
      }else if ( undefined !== attachments[0]['sizes']['thumnail'] ) {
        url = attachments[0]['sizes']['thumnail']['url'];
      }else{
        url = attachments[0]['sizes']['full']['url'];
      }

      selector.val( attachments[0]['id'] );
      $( mediaControl.container ).find( 'img' ).remove();
      $( mediaControl.container ).find( 'label' ).after( '<img src="' + url + '">' );
      selector.change();

    },

    init: function() {
      var context = $( '#wpbody, .wp-customizer' );
      context.on( 'click', '.tyche-media-control > .upload-button',
          function( e ) {
            e.preventDefault();
            var container = $( this ).parent(),
                sibling = container.find( '.image-id' ),
                id = sibling.attr( 'id' );

            mediaControl.size = $( '[data-delegate="' + id + '"]' ).val();
            mediaControl.container = container;
            mediaControl.selector = '#' + id;
            mediaControl.frame().open();
          } );

      context.on( 'click', '.tyche-media-control > .remove-button',
          function( e ) {
            e.preventDefault();
            var container = $( this ).parent(),
                sibling = container.find( '.image-id' ),
                img = container.find( 'img' );

            img.remove();
            sibling.val( '' ).trigger( 'change' );
          } );
    }
  };

  mediaControl.init();
} );

jQuery( document ).on( 'click', '.widget-layouts > a', function( e ) {
  var layout = jQuery( this ).data( 'layout' ),
      select = jQuery( this ).parent().siblings( '.layout-select' ),
      options = select.find( 'option' ),
      siblings = jQuery( this ).siblings( 'a' );

  jQuery.each( siblings, function() {
    if ( jQuery( this ).hasClass( 'selected' ) ) {
      jQuery( this ).removeClass( 'selected' );
    }
  } );

  jQuery( this ).addClass( 'selected' );

  jQuery.each( options, function() {
    if ( jQuery( this )[ 0 ].hasAttribute( 'selected' ) ) {
      jQuery( this ).removeAttr( 'selected' );
    }
  } );

  select.val( layout );
  select.trigger( 'change' );

} );