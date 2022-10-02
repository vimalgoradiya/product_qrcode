<?php

namespace Drupal\product_code\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 *
 * Create block which is diplay on landing page of product
 *
 * @Block(
 *   id = "generate_product_code",
 *   admin_label = @Translation("Generate Product QR-Code")
 * )
 *
 **/
 class ProductCode extends BlockBase{
  /**
   * {@inheritdoc}
   *
   **/
  public function build(){
    // To get the base url
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $current_url = Url::fromRoute('<current>');
    $path = $current_url->toString();
    $productURL = $host.$path;
    \Drupal::logger('product_code')->notice('<pre><code>' . print_r($productURL, TRUE) . '</code></pre>');
    $qrcodePath = $this->generateQRCode($productURL);

    return [
      '#markup' => '<img title="Product Image" src="'.$qrcodePath['url'].'" />',
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  protected function generateQRCode($productURL){
    // The below code will automatically create the path for the img.
    $currentUserID = \Drupal::currentUser()->id();
    \Drupal::logger('product_code')->notice('<pre><code>' . print_r($currentUserID, TRUE) . '</code></pre>');
    $qrcodePath = '';
    $qrCodeDirectory = "public://Images/QrCodes/";
    \Drupal::service('file_system')->prepareDirectory($qrCodeDirectory, \Drupal\Core\File\FileSystemInterface::CREATE_DIRECTORY);
    // Name of the generated image.
    $uri = $qrCodeDirectory . 'myQrcode_'.  $currentUserID . '.png'; // Generates a png image.
    $qrcodePath = \Drupal::service('file_system')->realpath($uri);
    // Generate QR code image.
    \PHPQRCode\QRcode::png($productURL, $qrcodePath, 'L', 4, 2);
    //return $path;
    $qrcodeURL = file_create_url($uri);
    return array('path' => $qrcodePath, 'url' => $qrcodeURL);
  }
  
 }