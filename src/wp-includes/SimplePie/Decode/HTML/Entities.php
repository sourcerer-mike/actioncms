<?php

/**
 * SimplePie
 *
 * A PHP-Based RSS and Atom Feed Framework.
 * Takes the hard work out of managing a complete RSS/Atom solution.
 *
 * Copyright (c) 2004-2012, Ryan Parman, Geoffrey Sneddon, Ryan McCue, and contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 *
 * 	* Redistributions of source code must retain the above copyright notice, this list of
 * 	  conditions and the following disclaimer.
 *
 * 	* Redistributions in binary form must reproduce the above copyright notice, this list
 * 	  of conditions and the following disclaimer in the documentation and/or other materials
 * 	  provided with the distribution.
 *
 * 	* Neither the name of the SimplePie Team nor the names of its contributors may be used
 * 	  to endorse or promote products derived from this software without specific prior
 * 	  written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS
 * OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 * AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS
 * AND CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
 * OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package SimplePie
 * @version 1.3.1
 * @copyright 2004-2012 Ryan Parman, Geoffrey Sneddon, Ryan McCue
 * @author Ryan Parman
 * @author Geoffrey Sneddon
 * @author Ryan McCue
 * @link http://simplepie.org/ SimplePie
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
/**
 * Decode HTML Entities
 *
 * This implements HTML5 as of revision 967 (2007-06-28)
 *
 * @deprecated Use DOMDocument instead!
 * @package SimplePie
 */
class SimplePie_Decode_HTML_Entities
{
    /**
     * Data to be parsed
     *
     * @access private
     * @var string
     */
    var $data = '';
    /**
     * Currently consumed bytes
     *
     * @access private
     * @var string
     */
    var $consumed = '';
    /**
     * Position of the current byte being parsed
     *
     * @access private
     * @var int
     */
    var $position = 0;
    /**
     * Create an instance of the class with the input data
     *
     * @access public
     * @param string $data Input data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * Parse the input data
     *
     * @access public
     * @return string Output data
     */
    public function parse()
    {
        while (($this->position = strpos($this->data, '&', $this->position)) !== false) {
            $this->consume();
            $this->entity();
            $this->consumed = '';
        }
        return $this->data;
    }
    /**
     * Consume the next byte
     *
     * @access private
     * @return mixed The next byte, or false, if there is no more data
     */
    public function consume()
    {
        if (isset($this->data[$this->position])) {
            $this->consumed .= $this->data[$this->position];
            return $this->data[$this->position++];
        } else {
            return false;
        }
    }
    /**
     * Consume a range of characters
     *
     * @access private
     * @param string $chars Characters to consume
     * @return mixed A series of characters that match the range, or false
     */
    public function consume_range($chars)
    {
        if ($len = strspn($this->data, $chars, $this->position)) {
            $data = substr($this->data, $this->position, $len);
            $this->consumed .= $data;
            $this->position += $len;
            return $data;
        } else {
            return false;
        }
    }
    /**
     * Unconsume one byte
     *
     * @access private
     */
    public function unconsume()
    {
        $this->consumed = substr($this->consumed, 0, -1);
        $this->position--;
    }
    /**
     * Decode an entity
     *
     * @access private
     */
    public function entity()
    {
        switch ($this->consume()) {
            case "\t":
            case "\n":
            case "\v":
            case "\v":
            case "\f":
            case " ":
            case "<":
            case "&":
            case false:
                break;
            case "#":
                switch ($this->consume()) {
                    case "x":
                    case "X":
                        $range = '0123456789ABCDEFabcdef';
                        $hex = true;
                        break;
                    default:
                        $range = '0123456789';
                        $hex = false;
                        $this->unconsume();
                        break;
                }
                if ($codepoint = $this->consume_range($range)) {
                    static $windows_1252_specials = array(0xd => "\n", 0x80 => "€", 0x81 => "�", 0x82 => "‚", 0x83 => "ƒ", 0x84 => "„", 0x85 => "…", 0x86 => "†", 0x87 => "‡", 0x88 => "ˆ", 0x89 => "‰", 0x8a => "Š", 0x8b => "‹", 0x8c => "Œ", 0x8d => "�", 0x8e => "Ž", 0x8f => "�", 0x90 => "�", 0x91 => "‘", 0x92 => "’", 0x93 => "“", 0x94 => "”", 0x95 => "•", 0x96 => "–", 0x97 => "—", 0x98 => "˜", 0x99 => "™", 0x9a => "š", 0x9b => "›", 0x9c => "œ", 0x9d => "�", 0x9e => "ž", 0x9f => "Ÿ");
                    if ($hex) {
                        $codepoint = hexdec($codepoint);
                    } else {
                        $codepoint = intval($codepoint);
                    }
                    if (isset($windows_1252_specials[$codepoint])) {
                        $replacement = $windows_1252_specials[$codepoint];
                    } else {
                        $replacement = SimplePie_Misc::codepoint_to_utf8($codepoint);
                    }
                    if (!in_array($this->consume(), array(';', false), true)) {
                        $this->unconsume();
                    }
                    $consumed_length = strlen($this->consumed);
                    $this->data = substr_replace($this->data, $replacement, $this->position - $consumed_length, $consumed_length);
                    $this->position += strlen($replacement) - $consumed_length;
                }
                break;
            default:
                static $entities = array('Aacute' => "Á", 'aacute' => "á", 'Aacute;' => "Á", 'aacute;' => "á", 'Acirc' => "Â", 'acirc' => "â", 'Acirc;' => "Â", 'acirc;' => "â", 'acute' => "´", 'acute;' => "´", 'AElig' => "Æ", 'aelig' => "æ", 'AElig;' => "Æ", 'aelig;' => "æ", 'Agrave' => "À", 'agrave' => "à", 'Agrave;' => "À", 'agrave;' => "à", 'alefsym;' => "ℵ", 'Alpha;' => "Α", 'alpha;' => "α", 'AMP' => "&", 'amp' => "&", 'AMP;' => "&", 'amp;' => "&", 'and;' => "∧", 'ang;' => "∠", 'apos;' => "'", 'Aring' => "Å", 'aring' => "å", 'Aring;' => "Å", 'aring;' => "å", 'asymp;' => "≈", 'Atilde' => "Ã", 'atilde' => "ã", 'Atilde;' => "Ã", 'atilde;' => "ã", 'Auml' => "Ä", 'auml' => "ä", 'Auml;' => "Ä", 'auml;' => "ä", 'bdquo;' => "„", 'Beta;' => "Β", 'beta;' => "β", 'brvbar' => "¦", 'brvbar;' => "¦", 'bull;' => "•", 'cap;' => "∩", 'Ccedil' => "Ç", 'ccedil' => "ç", 'Ccedil;' => "Ç", 'ccedil;' => "ç", 'cedil' => "¸", 'cedil;' => "¸", 'cent' => "¢", 'cent;' => "¢", 'Chi;' => "Χ", 'chi;' => "χ", 'circ;' => "ˆ", 'clubs;' => "♣", 'cong;' => "≅", 'COPY' => "©", 'copy' => "©", 'COPY;' => "©", 'copy;' => "©", 'crarr;' => "↵", 'cup;' => "∪", 'curren' => "¤", 'curren;' => "¤", 'Dagger;' => "‡", 'dagger;' => "†", 'dArr;' => "⇓", 'darr;' => "↓", 'deg' => "°", 'deg;' => "°", 'Delta;' => "Δ", 'delta;' => "δ", 'diams;' => "♦", 'divide' => "÷", 'divide;' => "÷", 'Eacute' => "É", 'eacute' => "é", 'Eacute;' => "É", 'eacute;' => "é", 'Ecirc' => "Ê", 'ecirc' => "ê", 'Ecirc;' => "Ê", 'ecirc;' => "ê", 'Egrave' => "È", 'egrave' => "è", 'Egrave;' => "È", 'egrave;' => "è", 'empty;' => "∅", 'emsp;' => " ", 'ensp;' => " ", 'Epsilon;' => "Ε", 'epsilon;' => "ε", 'equiv;' => "≡", 'Eta;' => "Η", 'eta;' => "η", 'ETH' => "Ð", 'eth' => "ð", 'ETH;' => "Ð", 'eth;' => "ð", 'Euml' => "Ë", 'euml' => "ë", 'Euml;' => "Ë", 'euml;' => "ë", 'euro;' => "€", 'exist;' => "∃", 'fnof;' => "ƒ", 'forall;' => "∀", 'frac12' => "½", 'frac12;' => "½", 'frac14' => "¼", 'frac14;' => "¼", 'frac34' => "¾", 'frac34;' => "¾", 'frasl;' => "⁄", 'Gamma;' => "Γ", 'gamma;' => "γ", 'ge;' => "≥", 'GT' => ">", 'gt' => ">", 'GT;' => ">", 'gt;' => ">", 'hArr;' => "⇔", 'harr;' => "↔", 'hearts;' => "♥", 'hellip;' => "…", 'Iacute' => "Í", 'iacute' => "í", 'Iacute;' => "Í", 'iacute;' => "í", 'Icirc' => "Î", 'icirc' => "î", 'Icirc;' => "Î", 'icirc;' => "î", 'iexcl' => "¡", 'iexcl;' => "¡", 'Igrave' => "Ì", 'igrave' => "ì", 'Igrave;' => "Ì", 'igrave;' => "ì", 'image;' => "ℑ", 'infin;' => "∞", 'int;' => "∫", 'Iota;' => "Ι", 'iota;' => "ι", 'iquest' => "¿", 'iquest;' => "¿", 'isin;' => "∈", 'Iuml' => "Ï", 'iuml' => "ï", 'Iuml;' => "Ï", 'iuml;' => "ï", 'Kappa;' => "Κ", 'kappa;' => "κ", 'Lambda;' => "Λ", 'lambda;' => "λ", 'lang;' => "〈", 'laquo' => "«", 'laquo;' => "«", 'lArr;' => "⇐", 'larr;' => "←", 'lceil;' => "⌈", 'ldquo;' => "“", 'le;' => "≤", 'lfloor;' => "⌊", 'lowast;' => "∗", 'loz;' => "◊", 'lrm;' => "‎", 'lsaquo;' => "‹", 'lsquo;' => "‘", 'LT' => "<", 'lt' => "<", 'LT;' => "<", 'lt;' => "<", 'macr' => "¯", 'macr;' => "¯", 'mdash;' => "—", 'micro' => "µ", 'micro;' => "µ", 'middot' => "·", 'middot;' => "·", 'minus;' => "−", 'Mu;' => "Μ", 'mu;' => "μ", 'nabla;' => "∇", 'nbsp' => " ", 'nbsp;' => " ", 'ndash;' => "–", 'ne;' => "≠", 'ni;' => "∋", 'not' => "¬", 'not;' => "¬", 'notin;' => "∉", 'nsub;' => "⊄", 'Ntilde' => "Ñ", 'ntilde' => "ñ", 'Ntilde;' => "Ñ", 'ntilde;' => "ñ", 'Nu;' => "Ν", 'nu;' => "ν", 'Oacute' => "Ó", 'oacute' => "ó", 'Oacute;' => "Ó", 'oacute;' => "ó", 'Ocirc' => "Ô", 'ocirc' => "ô", 'Ocirc;' => "Ô", 'ocirc;' => "ô", 'OElig;' => "Œ", 'oelig;' => "œ", 'Ograve' => "Ò", 'ograve' => "ò", 'Ograve;' => "Ò", 'ograve;' => "ò", 'oline;' => "‾", 'Omega;' => "Ω", 'omega;' => "ω", 'Omicron;' => "Ο", 'omicron;' => "ο", 'oplus;' => "⊕", 'or;' => "∨", 'ordf' => "ª", 'ordf;' => "ª", 'ordm' => "º", 'ordm;' => "º", 'Oslash' => "Ø", 'oslash' => "ø", 'Oslash;' => "Ø", 'oslash;' => "ø", 'Otilde' => "Õ", 'otilde' => "õ", 'Otilde;' => "Õ", 'otilde;' => "õ", 'otimes;' => "⊗", 'Ouml' => "Ö", 'ouml' => "ö", 'Ouml;' => "Ö", 'ouml;' => "ö", 'para' => "¶", 'para;' => "¶", 'part;' => "∂", 'permil;' => "‰", 'perp;' => "⊥", 'Phi;' => "Φ", 'phi;' => "φ", 'Pi;' => "Π", 'pi;' => "π", 'piv;' => "ϖ", 'plusmn' => "±", 'plusmn;' => "±", 'pound' => "£", 'pound;' => "£", 'Prime;' => "″", 'prime;' => "′", 'prod;' => "∏", 'prop;' => "∝", 'Psi;' => "Ψ", 'psi;' => "ψ", 'QUOT' => "\"", 'quot' => "\"", 'QUOT;' => "\"", 'quot;' => "\"", 'radic;' => "√", 'rang;' => "〉", 'raquo' => "»", 'raquo;' => "»", 'rArr;' => "⇒", 'rarr;' => "→", 'rceil;' => "⌉", 'rdquo;' => "”", 'real;' => "ℜ", 'REG' => "®", 'reg' => "®", 'REG;' => "®", 'reg;' => "®", 'rfloor;' => "⌋", 'Rho;' => "Ρ", 'rho;' => "ρ", 'rlm;' => "‏", 'rsaquo;' => "›", 'rsquo;' => "’", 'sbquo;' => "‚", 'Scaron;' => "Š", 'scaron;' => "š", 'sdot;' => "⋅", 'sect' => "§", 'sect;' => "§", 'shy' => "­", 'shy;' => "­", 'Sigma;' => "Σ", 'sigma;' => "σ", 'sigmaf;' => "ς", 'sim;' => "∼", 'spades;' => "♠", 'sub;' => "⊂", 'sube;' => "⊆", 'sum;' => "∑", 'sup;' => "⊃", 'sup1' => "¹", 'sup1;' => "¹", 'sup2' => "²", 'sup2;' => "²", 'sup3' => "³", 'sup3;' => "³", 'supe;' => "⊇", 'szlig' => "ß", 'szlig;' => "ß", 'Tau;' => "Τ", 'tau;' => "τ", 'there4;' => "∴", 'Theta;' => "Θ", 'theta;' => "θ", 'thetasym;' => "ϑ", 'thinsp;' => " ", 'THORN' => "Þ", 'thorn' => "þ", 'THORN;' => "Þ", 'thorn;' => "þ", 'tilde;' => "˜", 'times' => "×", 'times;' => "×", 'TRADE;' => "™", 'trade;' => "™", 'Uacute' => "Ú", 'uacute' => "ú", 'Uacute;' => "Ú", 'uacute;' => "ú", 'uArr;' => "⇑", 'uarr;' => "↑", 'Ucirc' => "Û", 'ucirc' => "û", 'Ucirc;' => "Û", 'ucirc;' => "û", 'Ugrave' => "Ù", 'ugrave' => "ù", 'Ugrave;' => "Ù", 'ugrave;' => "ù", 'uml' => "¨", 'uml;' => "¨", 'upsih;' => "ϒ", 'Upsilon;' => "Υ", 'upsilon;' => "υ", 'Uuml' => "Ü", 'uuml' => "ü", 'Uuml;' => "Ü", 'uuml;' => "ü", 'weierp;' => "℘", 'Xi;' => "Ξ", 'xi;' => "ξ", 'Yacute' => "Ý", 'yacute' => "ý", 'Yacute;' => "Ý", 'yacute;' => "ý", 'yen' => "¥", 'yen;' => "¥", 'yuml' => "ÿ", 'Yuml;' => "Ÿ", 'yuml;' => "ÿ", 'Zeta;' => "Ζ", 'zeta;' => "ζ", 'zwj;' => "‍", 'zwnj;' => "‌");
                for ($i = 0, $match = null; $i < 9 && $this->consume() !== false; $i++) {
                    $consumed = substr($this->consumed, 1);
                    if (isset($entities[$consumed])) {
                        $match = $consumed;
                    }
                }
                if ($match !== null) {
                    $this->data = substr_replace($this->data, $entities[$match], $this->position - strlen($consumed) - 1, strlen($match) + 1);
                    $this->position += strlen($entities[$match]) - strlen($consumed) - 1;
                }
                break;
        }
    }
}