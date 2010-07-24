//Clear search box on focus.
$( document ).ready( function(){
    $( '#search-box' ).focus( function(){
      if( $(this).val() == 'Search' ){
      $(this).val('');
      }
      });
    });

//Return "search" text on blur if search box left empty.
$( document ).ready( function(){
    $( '#search-box' ).blur( function(){
      if( $(this).val() == '' ){
      $(this).val( 'Search' );
      }
      });
    })
