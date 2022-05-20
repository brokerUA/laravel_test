<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailRFC2822 implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $crlf           = "(?:\r\n)";
        $wsp            = "[\t ]";
        $text           = "[\\x01-\\x09\\x0B\\x0C\\x0E-\\x7F]";
        $quoted_pair    = "(?:\\\\$text)";
        $fws            = "(?:(?:$wsp*$crlf)?$wsp+)";
        $ctext          = "[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F!-'*-[\\]-\\x7F]";
        $comment        = "(\\((?:$fws?(?:$ctext|$quoted_pair|(?1)))*$fws?\\))";
        $cfws           = "(?:(?:$fws?$comment)*(?:(?:$fws?$comment)|$fws))";
        //$cfws           = $fws; //an alternative to comments
        $atext          = "[!#-'*+\\-\\/0-9=?A-Z\\^-~]";
        $atom           = "(?:$cfws?$atext+$cfws?)";
        $dot_atom_text  = "(?:$atext+(?:\\.$atext+)*)";
        $dot_atom       = "(?:$cfws?$dot_atom_text$cfws?)";
        $qtext          = "[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F!#-[\\]-\\x7F]";
        $qcontent       = "(?:$qtext|$quoted_pair)";
        $quoted_string  = "(?:$cfws?\"(?:$fws?$qcontent)*$fws?\"$cfws?)";
        $dtext          = "[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F!-Z\\^-\\x7F]";
        $dcontent       = "(?:$dtext|$quoted_pair)";
        $domain_literal = "(?:$cfws?\\[(?:$fws?$dcontent)*$fws?]$cfws?)";
        $domain         = "(?:$dot_atom|$domain_literal)";
        $local_part     = "(?:$dot_atom|$quoted_string)";
        $addr_spec      = "($local_part@$domain)";
        $display_name   = "(?:(?:$atom|$quoted_string)+)";
        $angle_addr     = "(?:$cfws?<$addr_spec>$cfws?)";
        $name_addr      = "(?:$display_name?$angle_addr)";
        $mailbox        = "(?:$name_addr|$addr_spec)";
        $mailbox_list   = "(?:(?:(?:(?<=:)|,)$mailbox)+)";
        $group          = "(?:$display_name:(?:$mailbox_list|$cfws)?;$cfws?)";
        $address        = "(?:$mailbox|$group)";
        $address_list   = "(?:(?:^|,)$address)+";

        return preg_match("/^$address_list$/", $value) === 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be passed RFC 2822 standard.';
    }
}
