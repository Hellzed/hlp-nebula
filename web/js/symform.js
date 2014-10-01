$(document).ready(function() {



  var $form = $('form');
  var $index = new Array();
  
  findCollections($form);
  findSelect($form);
  findDepId($form);
  findDepPkgs($form);
  
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
             alert($(this).attr('id'));
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
          if($index[$fieldId] == 0) {
            addField($field, $prototype, $fieldId);
          }
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
    findSelect($prototype);
    findDepId($prototype);
    findDepPkgs($prototype);
  }
  
  function findSelect($element) {
    var $select = $element.find('[id$=type]');
    
    
    $select.each(function(index) {
      var $fieldId = $(this).attr('id');
      var $strippedId = $fieldId.split("_").pop(-1);
      var $container = $(this).closest('[id$=actions]');
      
      if($strippedId == 'type' && typeof $container !== undefined && $container !== false) {
      
        $(this).change(function(e) {
          var $value = $(this).val();
          
          var $destField = $(this).closest('.well').children(':nth-child(3)')
          if($value == 'delete') {
            $destField.attr('hidden', 'hidden');
          } else {
            $destField.removeAttr('hidden');
          }
        });
      }
    });
  }
  
  function findDepId($element) {
    var $depId = $element.find('[id$=depId]');
    
    $depId.each(function(index) {
      var $fieldId = $(this).attr('id');
      var $strippedId = $fieldId.split("_").pop(-1);
      var $container = $(this).closest('[id$=dependencies]');
      if($strippedId == 'depId' && typeof $container !== undefined && $container !== false) {
      
        $(this).autocomplete({
            source : 'http://localhost/Symfony/web/app_dev.php/nebula/ajax/search_mods',
            minLength: 1,
            delay: 200,
        });
        
        $(this).change(function (index) {
          findDepPkgs($(this).closest('.well'));
        });
      }
    });
  }
  
  function findDepPkgs($element) {
    $element.find('.depPkgItem').each(function(index) {
      $depPkgInput = $(this).find('input');
      $currentModSearch = $depPkgInput.closest('.well').find('[id$="depId"]').val();
      
      $depPkgInput.autocomplete({
            source : 'http://localhost/Symfony/web/app_dev.php/nebula/ajax/'+$currentModSearch+'/search_packages',
            minLength: 0,
            delay: 200

      });
      $depPkgInput.focus(function(index) {
        var $searchTerm = $depPkgInput.val();
        if($searchTerm.length == 0) {
          $depPkgInput.autocomplete( "search", $searchTerm );
        } else {
          $depPkgInput.autocomplete( "search", "");
        }
      });
    });
  }
   
  
      
  
  
  
});
