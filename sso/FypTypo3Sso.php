<?php

// part of FREMO Yellow Pages @ g-zi.de/FYP

class SsoSignatureVerifier
{
    private function stringMatchesSignatureInKey($data, $signature, $opensslKeyFile) {
        $fp = @fopen($opensslKeyFile, "r");

        if ($fp) {
            $cert = fread($fp, 8192);
            fclose($fp);

            $pubkeyid = openssl_get_publickey($cert);

            $ok = @openssl_verify($data, $signature, $pubkeyid);

            @openssl_free_key($pubkeyid);

            return $ok == 1;
        }

        return false;
    }

    private function hex2bin($data) {
        $len = strlen($data);
        $newdata='';
        for ($i=0; $i < $len; $i+=2) {
            $newdata .=  pack("C",hexdec(substr($data,$i,2)));
        }
        return $newdata;
    }

    public function ssoSignatureIsValid($version, $user, $tpa_id, $expires, $action, $flags, $userdata, $signature) {
        $toBeVerifiedWithSignature = 'version='.$version.'&user='.$user.'&tpa_id='.$tpa_id.'&expires='.$expires.'&action='.$action.'&flags='.$flags.'&userdata='.$userdata;

        return SsoSignatureVerifier::stringMatchesSignatureInKey($toBeVerifiedWithSignature, SsoSignatureVerifier::hex2bin($signature), 'sso/fremot3_public.key');
    }

    public function UnpackUserdata($userdata) {
        $result = array();
        foreach (explode('|', base64_decode($userdata)) as $kvp) {
            $intermediate = explode('=', $kvp);
            $result[$intermediate[0]] = $intermediate[1];
        }

        return $result;
    }

    public function x($d, $sig)
    {
        return SsoSignatureVerifier::stringMatchesSignatureInKey($d, SsoSignatureVerifier::hex2bin($sig), 'sso/fremot3_public.key');
    }
}
