<!doctype html>
<html><head><title>SimpleImage Tests</title><link rel="stylesheet" href="style.css"></head><body>
<?php
// write some system info on top of page
$info = [
    'PHP_VERSION' => PHP_VERSION,
    'PHP_OS' => PHP_OS,
    'PHP_OS_FAMILY' => PHP_OS_FAMILY,
    'PHP_SAPI' => PHP_SAPI,
    'gd_info()["GD Version"]' => gd_info()['GD Version'],
    'GD_BUNDLED' => GD_BUNDLED,
    'GD_VERSION' => GD_VERSION,
];
?>
<table>
    <tr><?php foreach ($info as $key => $val) { echo "<th>$key</th>\r\n"; } ?></tr>
    <tr><?php foreach ($info as $key => $val) { echo "<td>$val</td>\r\n"; } ?></tr>
</table>

<?php
/******************* SimpleImage Tests *******************/

// require_once('SimpleImage.php');
// require_once('SimpleImage3.3.3.php');
require_once('SimpleImageFixTranspRj.php');

// input and output dirs
$input = 'input';
$output = 'output';

// tests
$tests = [

//  ['method' => ['arg1', 'arg2']  ],
  
    // ['crop'       => [10, 10, 40, 40]],
    ['crop'       => [10, 20, 60, 90]],
    // ['crop'       => [20, 20, 80, 80]],
    ['resize'     => [40, 80]],
    
    // ['bestFit'    => [40, 80]],
    ['thumbnail'  => [60, 60, 'center']],
    
    ['rotate'     => [10]],
    // ['rotate'     => [45]],
    // ['rotate'     => [90]],
    // ['rotate'     => [180]],
    
    // ['autoOrient' => []],
    // ['bestFit'    => [40, 80]], // bestFit($maxWidth, $maxHeight)
    // ['crop'       => [20, 20, 80, 80]], // crop($x1, $y1, $x2, $y2)
    // ['flip'       => ['x']], // flip($direction) - x|y|both
    
    // ['maxColors'  => [256, true]], // maxColors($max, $dither)
    // ['overlay'   => [/* ... */]], // overlay($overlay, $anchor, $opacity, $xOffset, $yOffset)
    // ['resize'     => [40, 80]], // resize($width, $height)
    ['rotate'     => [45, 'green']], // rotate($angle, $backgroundColor)
    // ['text'     => [/* ... */]], // text($text, $options, &$boundary)
    // ['thumbnail'  => [60, 60, 'center']], // thumbnail($width, $height, $anchor)
    
    // drawing
    // to do ...
    
    // filters
    ['blur'       => ['gaussian', 1]], // blur($type, $passes)
    ['brighten'   => [50]], // brighten($percentage)
    // ['colorize'  => [/* ... */]], // colorize($color)
    ['contrast'   => [50]], // contrast($percentage)
    ['darken'     => [50]], // darken($percentage)
    ['desaturate'     => []],
    // ['duotone'     => [/* ... */]], // duotone($lightColor, $darkColor)
    ['edgeDetect'     => []],
    ['emboss'     => []],
    ['invert'     => []],
    ['opacity'    => [.5]],
    ['pixelate'    => [10]],
    ['sepia'     => []],
    ['sharpen'    => [10]],
    ['sketch'     => []],
    
    // deprecated
    // ['fitToHeight'    => [40]], // fitToHeight($height)
    // ['fitToWidth'    => [40]], // fitToWidth($width)
    
];

/******************* Test Output *******************/

if (!is_dir($output))
    mkdir ($output, 0755);// create output dir

$error = '';

$dir = new DirectoryIterator($input);


// sort DirectoryIterator manually to iterate alphabetically
$sorted = [];
foreach ($dir as $file) {
    if ($file->isDot() || $file->isDir()) continue;
    $sorted[] = new \SPLFileObject($file->getPathname());
}
usort($sorted, function($a, $b){
    // return strcmp($a->getFilename(), $b->getFilename());
    return strcmp(getimagesize($a->getPathname())['bits'], getimagesize($b->getPathname())['bits']);
});


// create images and html output
foreach ($sorted as $file) {

    echo "<div>";
    echo "<p><b>".$file->getFilename()."</b></p>";
    echo "<div><img src='".$file->getPathname()."' class='original'></div>";
    if ($info = getimagesize($file->getPathname())) {
        echo "<div><p class='info'>";
        foreach($info as $key => $val) {
            switch($key) {
              case '0': echo "<b>width:</b> $val<br>\r\n"; break;
              case '1': echo "<b>height:</b> $val<br>\r\n"; break;
              case '2': // skip the type number and width/height string
              case '3': break;
              default: echo "<b>$key:</b> $val<br>\r\n";
            }
        }
    }
    echo "</p></div>";

    foreach ($tests as $test) {
      
        foreach ($test as $call => $args) {

            $outputFileName = "{$call}_" . implode('_', $args) . '_' . $file->getFilename();

            try {

                $img = new \claviska\SimpleImage($file->getPathname());

            } catch (\Exception $e) {

                // display the same error (unsopported file) only once
                echo $error === $e->getMessage() ? '' : '<div><span>' . $e->getMessage() . '</span></div>';
                $error = $e->getMessage();
                continue;

            }

            try {

                $img->$call(...$args) // splat args, requires PHP 5.6+
                    ->toFile("$output/$outputFileName");

                echo "<div><img src='$output/$outputFileName' alt='$outputFileName' title='$outputFileName'><span>$call</span></div>";

            } catch (\Exception $e) {

                echo '<div><span>' . $e->getMessage() . '</span></div>'; continue;

            }

        }
    }

    echo "</div>";

}

?>
</body></html>
