$(document).ready(function() {

  var $form = $('form');
  var $index = new Array();
  
  findCollections($form);
  
  function findCollections($element) {
  
    $element.children('div').each(function(index) {
    
      var $prototype = $(this).attr('data-prototype');
      
      if (typeof $prototype !== typeof undefined && $prototype !== false) {
      
        var $field = $(this).parent();
        var $fieldId = $(this).attr('id');
        
        var $generatedChildren = $(this).children('div');
        var $generatedIndex = 0;
        
        $index[$fieldId] = 0;
        if ( $generatedChildren.length > 0 ) {
          $generatedChildren.each(function(index) {
             $generatedIndex = $(this).attr('id').split("_").pop(-1);
             
             if ( $generatedIndex >= $index[$fieldId]) {
               $index[$fieldId] = ~~$generatedIndex+1;
             }
             
             $(this).parent().append($(this));
          });
        }
        
        $(this).children('div').each(function(index) { 
          var $rmButton = $(this).find('.rmButton');
    
          $rmButton.click(function(e) {
                $(this).parent().parent().parent().remove();
                return false;
              });
        });
        
        var $addButton = $field.children(':last').has('.addButton').children().children('button');
        $addButton.parent().parent().appendTo($(this));
        $addButton.click(function(e) {
          addField($field, $prototype, $fieldId);
          return false;
        });
        
        if($fieldId.split("_").pop(-1) == 'urls' || $fieldId.split("_").pop(-1) == 'paths') {
          addField($field, $prototype, $fieldId);
        }
        
      }
      
      findCollections($(this));
      
    });
    
  }
  
  function addField($field, $prototype, $fieldId) {
    //Get the stripped field ID
    var $strippedId = $fieldId.split("_").pop(-1);
    //Define Regexps : one for the __$object_prototype__ , one for the  __$object_prototype__label__
    var $labelRegex = new RegExp('__' + $strippedId + '_prototype__label__','g');
    var $prototypeRegex = new RegExp('__' + $strippedId + '_prototype__','g');
    //Replace the __prototype__ strings in the $prototype
    $prototype = $prototype.replace($labelRegex, $index[$fieldId]).replace($prototypeRegex, $index[$fieldId]);
    //Make the prototype an HTML element
    $prototype = $($prototype);
    //Append it to the prototype storage div's parent

    $field.children('div[data-prototype]').children(':last').before($prototype);
    //increment the fields index
    $index[$fieldId]++;
    
    var $rmButton = $prototype.find('.rmButton');
    
    $rmButton.click(function(e) {
          $prototype.remove();
          return false;
        });
        
    findCollections($prototype);
    
    //findSelect($prototype, $strippedId)
  }
  /*
  function findSelect($prototype, $strippedId) {
    if($strippedId == 'actions') {
      var $select = $prototype.find('select');
      
      $select.change(function(e) {
          var $value = $select.val();
          var $destField = $select.parent().parent().children(':nth-child(3)')
          if($value == 'delete') {
            $destField.attr('hidden', 'hidden');
          } else {
            $destField.removeAttr('hidden');
          }
        });
    
    }
  }*/
  
  
});
