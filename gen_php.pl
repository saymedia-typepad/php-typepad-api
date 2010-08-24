#!/usr/bin/env perl
#
# Copyright (c) 2010 Six Apart Ltd.
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without
# modification, are permitted provided that the following conditions are met:
#
# * Redistributions of source code must retain the above copyright notice,
#   this list of conditions and the following disclaimer.
#
# * Redistributions in binary form must reproduce the above copyright notice,
#   this list of conditions and the following disclaimer in the documentation
#   and/or other materials provided with the distribution.
#
# * Neither the name of Six Apart Ltd. nor the names of its contributors may
#   be used to endorse or promote products derived from this software without
#   specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
# AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
# IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
# ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
# LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
# CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
# SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
# INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
# ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
# POSSIBILITY OF SUCH DAMAGE.

use strict;
use warnings;
use Data::Dumper;

use JSON;
use LWP::Simple;

my $api_base = 'http://api.typepad.com';
my $php_dir = 'lib/TypePad';
my $j = JSON->new;

open(LICENSE, '<LICENSE.txt') || die $!;
my $license = "/**\n";
while (my $line = <LICENSE>) {
    $license .= " * $line";
}
close LICENSE;
$license .= " */";

my $nouns_php = <<EOPHP;
/**
 * Noun classes for the TypePad API.
 *
 * AUTO-GENERATED FILE - DO NOT EDIT
 *
 * \@package TypePad-Nouns
 */

$license

EOPHP

my $raw_map = get("$api_base/client-library-helpers/method-mappings.json");
my $map = $j->decode($raw_map);

my $idget = "       if (!is_array(\$params)) \$params = array('id' => \$params);\n";
for my $noun (keys %$map) {
    my $functions = '';
    my $url_noun = lcfirst($noun);
    $url_noun =~ s/([A-Z])/'-' . lc($1)/eg;
    for my $endpoint (@{$map->{$noun}->{methods}}) {
        my $path_chunks = $endpoint->{pathChunks};
        my %path_params = reverse %{$endpoint->{pathParams}};

        my $i = -1;
        my $php_chunks = join(', ', map {
            $i++;
            $_ ? "'$_'" : "\$params['$path_params{$i}']"
        } @$path_chunks);
        my $doc_path = join('/', map {
            defined($_) ? $_ : '<id>'
        } @$path_chunks);
        my $param_doc = join(', ',
            (map {"$_ => string" }
            sort { $endpoint->{pathParams}->{$a} <=> $endpoint->{pathParams}->{$b} }
            keys %{$endpoint->{pathParams}}),
            ($endpoint->{httpMethod} =~ /^(POST|PUT)$/) ? ('payload => array') : ()
        );

        my $query_params = '';
        for my $key (keys %{$endpoint->{queryParams}}) {
            my $plusone = ($key eq 'offset') ? ' + 1' : '';
            $query_params .= "\n        if (array_key_exists('$key', \$params)) \$query_params['$endpoint->{queryParams}->{$key}'] = \$params['$key']$plusone;";
        }
        my $rot = $endpoint->{returnObjectType};
        my $return_type = '';
        my $return;
        if ($rot && $rot->{name}) {
            $return_type = $rot->{name};
        } elsif ($rot) {
            # TODO: if we ever have an action endpoint that returns more than
            # one property, we'll need to figure out how to support that
            my $prop = ($rot->{properties}->[0]);
            $return_type = "$prop->{name}:$prop->{type}";
            $return = "$prop->{type} $prop->{docString}";
        }
        my $lcmethod = lc($endpoint->{httpMethod});
        my $idcase = (
            ($endpoint->{httpMethod} =~ /GET|DELETE/)
            && (scalar(keys %{$endpoint->{pathParams}}) == 1)
        ) ? $idget : '';
        $endpoint->{docString} ||= '';
        $return ||= $return_type || '';
        if ($return) {
            if ($return =~ /List<(.)/) {
                my $tp = ($1 =~ /[A-Z]/) ? 'TP' : '';
                $return =~ s/List</TPList TPList of $tp/;
                $return =~ s/>//;
            } else {
                $return = "TP$return";
            }
            $return = "\n     * \@return $return";
        }
        $functions .= <<EOPHP;
    /**
     * $endpoint->{docString}
     *
     * \@link http://www.typepad.com/services/apidocs/endpoints/$doc_path$return
     * \@param array \$params array($param_doc)
     */
    function $endpoint->{methodName}(\$params) {
$idcase        \$path_chunks = array($php_chunks);
EOPHP
        if ($endpoint->{httpMethod} =~ /^POST|PUT$/) {
            $functions .= <<EOPHP;
        return \$this->typepad->$lcmethod(\$path_chunks, \$params['payload'], '$return_type');
EOPHP
        } elsif ($endpoint->{httpMethod} eq 'DELETE') {
            $functions .= <<EOPHP;
        return \$this->typepad->$lcmethod(\$path_chunks, '$return_type');
EOPHP
        } else {
            $functions .= <<EOPHP;
        \$query_params = array();$query_params
        return \$this->typepad->$lcmethod(\$path_chunks, \$query_params, '$return_type');
EOPHP
        }
        $functions .= "    }\n\n";
    }
    my $class = 'TP' . ucfirst($noun);
    $nouns_php .= <<EOPHP;
/**
 * \@package TypePad-Nouns
 * \@subpackage $class
 */
class $class extends TPNoun {

$functions}
TypePad::addNoun('$noun');

EOPHP
}

write_file('Nouns', $nouns_php);

my $raw_types = get("$api_base/client-library-helpers/object-types.json");
my $types = $j->decode($raw_types);

my %types_php;
my %children;

# thanks to PHP's startlingly broken inheritance model, we need to duplicate
# this code in every subclass.
my $functions = '    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }
';

# need to run through them all first so we can identify abstract classes
for my $type (@{$types->{entries}}) {
    my $class = "TP$type->{name}";
    my $parent = $type->{parentType} ? "TP$type->{parentType}" : 'TPObject';
    $children{$parent} ||= [];
    push @{$children{$parent}}, $class;
}

for my $type (@{$types->{entries}}) {
    my $class = "TP$type->{name}";
    my $parent = $type->{parentType} ? "TP$type->{parentType}" : 'TPObject';
    my %seen;
    my %sub_objects;
    my %properties;
    my %prop_types;
    for my $prop (@{$type->{properties}}) {
        # workaround for duplicates, Bugid:94954
        next if $seen{$prop->{name}};
        $seen{$prop->{name}}++;
        if ($prop->{type} =~ /^[A-Z]|array<[A-Z]/) {
            $sub_objects{$prop->{name}} = $prop->{type};
        }
        $prop->{docString} =~ s/'/\\'/g;
        $properties{$prop->{name}} = $prop->{docString};
        $prop_types{$prop->{name}} = $prop->{type};
    }
    my $fulfill = '';
    if (%sub_objects) {
        for my $prop (keys %sub_objects) {
            if ($sub_objects{$prop} =~ /array<([^>]+)>/) {
                $fulfill .= <<EOPHP;
        if (isset(\$data->$prop)) \$this->$prop = new TPList(\$data->$prop, 'TP$1');
EOPHP
            } else {
                $fulfill .= <<EOPHP;
        if (isset(\$data->$prop) && (get_class(\$data->$prop) == 'stdClass')) {
            \$ot_class = 'TP' . (isset(\$data->$prop->objectType) ? \$data->$prop->objectType : '$sub_objects{$prop}');
            \$this->$prop = new \$ot_class(\$data->$prop);
        }
EOPHP
            }
        }
        $fulfill = <<EOPHP;
    function fulfill(\$data) {
        parent::fulfill(\$data);
$fulfill    }
EOPHP
    }
    my $properties = join(",\n        ", map { "'$_' => array('$properties{$_}', '$prop_types{$_}')" } keys %properties);
    my $abstract = $children{$class} ? 'true' : 'false';
    my $a_n = ($type->{name} =~ /^[aeiou]/i) ? 'An' : 'A';
    my $type_php = <<EOPHP;
/**
 * $a_n $type->{name} object from the TypePad API.
 *
 * \@link http://www.typepad.com/services/apidocs/objecttypes/$type->{name}
 * \@package TypePad-ObjectTypes
 * \@subpackage $class
 */
class $class extends $parent {

    protected static \$properties = array(
        $properties
    );

$functions
    static function isAbstract() { return $abstract; }

$fulfill}

EOPHP
    $types_php{$class} = $type_php;
}

my $types_php = <<"EOPHP";
/**
 * Object type classes for the TypePad API.
 *
 * AUTO-GENERATED FILE - DO NOT EDIT
 *
 * \@package TypePad-ObjectTypes
 */

$license

EOPHP

# we need a flat list of classes such that a parent class will precede
# its children in the source
for my $class (@{children_flat('TPObject', \%children)}) {
    $types_php .= $types_php{$class};
}

write_file('ObjectTypes', $types_php);

sub children_flat {
    my ($key, $children) = @_;
    my @ret;
    for my $child (@{$children->{$key}}) {
        push(@ret, $child);
        if ($children->{$child}) {
            push(@ret, @{children_flat($child, $children)});
        }
    }
    return \@ret;

}

sub write_file {
    my ($filename, $content) = @_;
    open(FILE, ">$php_dir/$filename.php") || die $!;
    print FILE "<?php\n\n$content";
    close FILE;
}
