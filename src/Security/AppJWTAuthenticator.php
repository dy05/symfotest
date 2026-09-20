<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;

class AppJWTAuthenticator extends JWTAuthenticator
{
//    /**
//     * @return TokenExtractorInterface
//     */
//    protected function getTokenExtractor(): TokenExtractorInterface
//    {
////        // Return a custom extractor, no matter of what are configured
////        return new AuthorizationHeaderTokenExtractor('Token', 'Authorization');
//
//        // Or retrieve the chain token extractor for mapping/unmapping extractors for this authenticator
//        $chainExtractor = parent::getTokenExtractor();
//
////        // Clear the token extractor map from all configured extractors
////        $chainExtractor->clearMap();
//
////        // Or only remove a specific extractor
////        $chainTokenExtractor->removeExtractor(function (TokenExtractorInterface $extractor) {
////            return $extractor instanceof CookieTokenExtractor;
////        });
//
////        // Add a new query parameter extractor to the configured ones
////        $chainExtractor->addExtractor(new QueryParameterTokenExtractor('jwt'));
//
//        // Return the chain token extractor with the new map
////        return $chainTokenExtractor;
//        return $chainExtractor;
//    }
}
