<?php
// This provides wordpress with the ability to auto create an image after an upload that
// will maximize the size of the image according to the ratio provided



// a class that returns the biggest sizes for a specific ratio.
class ImageRatio {

  private $ratio;

  function __construct($ratioW = 4, $ratioH = 3) {
    $this->ratio = array($ratioW, $ratioH);
  }

  function getLargestSize($imgW, $imgH) {
    $inverse = false;
    // let's try to keep width and calculate new height  
    $newSize = round(($this->ratio[1] * $imgW) / $this->ratio[0]);
    if ($newSize > $imgH) {
      $inverse = true;
      // if the calculated height is bigger than actual size
      // let's keep current height and calculate new width
      $newSize = round(($this->ratio[0] * $imgH) / $this->ratio[1]);

    }
    return $inverse ? array( $newSize, $imgH ) : array( $imgW, $newSize );
  }
}

// adds a custom 1x1 image ratio that maximizes the size (with a max of 800px)
add_filter( 'intermediate_image_sizes_advanced', function( $sizes, $metadata ) {

  if (! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {
      // calculate the max width and height for the ratio
      $ratio = new ImageRatio( 1, 1 );
      list($width, $height) = $ratio->getLargestSize( 
        $metadata['width'],
        $metadata['height']
      );

      if ($width > 800 || $height > 800):
        $width = 800;
        $height = 800;
      endif;
      
      // let's add our custom size
      $sizes['1x1'] = array(
        'width'  => $width,
        'height' => $height,
        'crop'   => true
      );
  }

  return $sizes;
}, 10, 2 );

// adds a custom 16x9 image ratio that maximizes the size (with a max width of 1280px)
add_filter( 'intermediate_image_sizes_advanced', function( $sizes, $metadata ) {

  if (! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {
      // calculate the max width and height for the ratio
      $ratio = new ImageRatio( 16, 9 );

      // limit the width if it is too large
      if ($metadata['width'] > 1280) {
        $metadata['height'] = (1280 / $metadata['width']) * $metadata['height']; 
        $metadata['width'] = 1280;
      }

      list($width, $height) = $ratio->getLargestSize( 
        $metadata['width'],
        $metadata['height']
      );
      
      // let's add our custom size
      $sizes['16x9'] = array(
        'width'  => $width,
        'height' => $height,
        'crop'   => true
      );
  }

  return $sizes;
}, 10, 2 );

// adds a custom 3x2 image ratio that maximizes the size (with a max width of 1280px)
add_filter( 'intermediate_image_sizes_advanced', function( $sizes, $metadata ) {

  if (! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {
      // calculate the max width and height for the ratio
      $ratio = new ImageRatio( 3, 2 );

      // limit the width if it is too large
      if ($metadata['width'] > 1280) {
        $metadata['height'] = (1280/$metadata['width']) * $metadata['height']; 
        $metadata['width'] = 1280;
      }

      list($width, $height) = $ratio->getLargestSize( 
        $metadata['width'],
        $metadata['height']
      );
      
      // let's add our custom size
      $sizes['3x2'] = array(
        'width'  => $width,
        'height' => $height,
        'crop'   => true
      );
  }

  return $sizes;
}, 10, 2 );

add_image_size( 'featured-image', 1000);

?>