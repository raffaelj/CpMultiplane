<?php

namespace Multiplane\Helper;

class SEO extends \Lime\Helper {

    public function imageUrl($img, $all = true) {

        // image url
        if (is_string($img)) return $img; // to do pathToUrl

        if (is_array($img)) {

            // asset
            if (isset($img['_id'])) {
                return $this->app['site_url'].'/getImage?src='.$img['_id'].'&w=1500&h=1500';
            }

            // gallery
            if (isset($img[0]) && isset($img[0]['meta']['asset'])) {

                if ($all) {
                    $images = [];
                    foreach ($img as $i) {
                        $images[] = [
                            'url' => $this->app['site_url'].'/getImage?src='.$i['meta']['asset'].'&w=1500&h=1500',
                            // to do width, height, type, alt text
                        ];
                    }
                    return $images;
                }

                // return first image from gallery
                else {
                    return $this->app['site_url'].'/getImage?src='.$img[0]['meta']['asset'].'&w=1500&h=1500';
                }

            }

        }

        return '';

    }

}
