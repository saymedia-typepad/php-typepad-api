<?php

class TPBase extends TPObject {

    protected static $properties = array(
        
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return true; }

}

class TPAccount extends TPBase {

    protected static $properties = array(
        'providerURL' => array('T<Deprecated> The URL of the home page of the service that provides this account.', 'string'),
        'providerIconURL' => array('T<Deprecated> The URL of a 16-by-16 pixel icon that represents the service that provides this account.', 'string'),
        'userId' => array('The machine identifier or primary key for the account, if known. (Some sites only have a M<username>.)', 'string'),
        'crosspostable' => array('C<true> if this account can be used to crosspost, or C<false> otherwise. An account can be used to crosspost if its service supports crossposting and the user has enabled crossposting for the specific account.', 'boolean'),
        'providerIconUrl' => array('The URL of a 16-by-16 pixel icon that represents the service that provides this account.', 'string'),
        'username' => array('The username of the account, if known. (Some sites only have a M<userId>.)', 'string'),
        'providerName' => array('A human-friendly name for the service that provides this account.', 'string'),
        'domain' => array('The DNS domain of the service that provides the account.', 'string'),
        'url' => array('The URL of the user\'s profile or primary page on the remote site, if known.', 'string'),
        'providerUrl' => array('The URL of the home page of the service that provides this account.', 'string'),
        'id' => array('A URI that serves as a globally unique identifier for the account.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPApiKey extends TPBase {

    protected static $properties = array(
        'owner' => array('The application that owns this API key.', 'Application'),
        'apiKey' => array('The actual API key string. Use this as the consumer key when making an OAuth request.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->owner) && (get_class($data->owner) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->owner->objectType) ? $data->owner->objectType : 'Application');
            $this->owner = new $ot_class($data->owner);
        }
    }
}

class TPAsset extends TPBase {

    protected static $properties = array(
        'source' => array('An object describing the site from which this asset was retrieved, if the asset was obtained from an external source.', 'AssetSource'),
        'excerpt' => array('A short, plain-text excerpt of the entry content. This is currently available only for O<Post> assets.', 'string'),
        'content' => array('The raw asset content. The M<textFormat> property describes how to format this data. Use this property to set the asset content in write operations. An asset posted in a group may have a M<content> value up to 10,000 bytes long, while a O<Post> asset in a blog may have up to 65,000 bytes of content.', 'string'),
        'favoriteCount' => array('The number of distinct users who have added this asset as a favorite.', 'integer'),
        'author' => array('The user who created the selected asset.', 'User'),
        'isFavoriteForCurrentUser' => array('C<true> if this asset is a favorite for the currently authenticated user, or C<false> otherwise. This property is omitted from responses to anonymous requests.', 'boolean'),
        'publicationStatus' => array('T<Editable> An object describing the visibility status and publication date for this asset. Only visibility status is editable.', 'PublicationStatus'),
        'renderedContent' => array('The content of this asset rendered to HTML. This is currently available only for O<Post> and O<Page> assets.', 'string'),
        'crosspostAccounts' => array('T<Editable> A set of identifiers for O<Account> objects to which to crosspost this asset when it\'s posted. This property is omitted when retrieving existing assets.', 'set<string>'),
        'objectType' => array('The keyword identifying the type of asset this is.', 'string'),
        'groups' => array('T<Deprecated> An array of strings containing the M<id> URI of the O<Group> object that this asset is mapped into, if any. This property has been superseded by the M<container> property.', 'array<string>'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of this asset. Only the one object type URI for the particular type of asset this asset is will be present.', 'set<string>'),
        'container' => array('An object describing the group or blog to which this asset belongs.', 'ContainerRef'),
        'description' => array('The description of the asset.', 'string'),
        'commentCount' => array('The number of comments that have been posted in reply to this asset. This number includes comments that have been posted in response to other comments.', 'integer'),
        'published' => array('The time at which the asset was created, as a W3CDTF timestamp.', 'string'),
        'permalinkUrl' => array('The URL that is this asset\'s permalink. This will be omitted if the asset does not have a permalink of its own (for example, if it\'s embedded in another asset) or if TypePad does not know its permalink.', 'string'),
        'textFormat' => array('A keyword that indicates what formatting mode to use for the content of this asset. This can be C<html> for assets the content of which is HTML, C<html_convert_linebreaks> for assets the content of which is HTML but where paragraph tags should be added automatically, or C<markdown> for assets the content of which is Markdown source. Other formatting modes may be added in future. Applications that present assets for editing should use this property to present an appropriate editor.', 'string'),
        'title' => array('The title of the asset.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return true; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'AssetSource');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->container) && (get_class($data->container) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->container->objectType) ? $data->container->objectType : 'ContainerRef');
            $this->container = new $ot_class($data->container);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
        if (isset($data->publicationStatus) && (get_class($data->publicationStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->publicationStatus->objectType) ? $data->publicationStatus->objectType : 'PublicationStatus');
            $this->publicationStatus = new $ot_class($data->publicationStatus);
        }
    }
}

class TPAudio extends TPAsset {

    protected static $properties = array(
        'source' => array('An object describing the site from which this asset was retrieved, if the asset was obtained from an external source.', 'AssetSource'),
        'excerpt' => array('A short, plain-text excerpt of the entry content. This is currently available only for O<Post> assets.', 'string'),
        'content' => array('The raw asset content. The M<textFormat> property describes how to format this data. Use this property to set the asset content in write operations. An asset posted in a group may have a M<content> value up to 10,000 bytes long, while a O<Post> asset in a blog may have up to 65,000 bytes of content.', 'string'),
        'favoriteCount' => array('The number of distinct users who have added this asset as a favorite.', 'integer'),
        'author' => array('The user who created the selected asset.', 'User'),
        'isFavoriteForCurrentUser' => array('C<true> if this asset is a favorite for the currently authenticated user, or C<false> otherwise. This property is omitted from responses to anonymous requests.', 'boolean'),
        'audioLink' => array('A link to the audio stream that is this Audio asset\'s content.', 'AudioLink'),
        'publicationStatus' => array('T<Editable> An object describing the visibility status and publication date for this asset. Only visibility status is editable.', 'PublicationStatus'),
        'renderedContent' => array('The content of this asset rendered to HTML. This is currently available only for O<Post> and O<Page> assets.', 'string'),
        'crosspostAccounts' => array('T<Editable> A set of identifiers for O<Account> objects to which to crosspost this asset when it\'s posted. This property is omitted when retrieving existing assets.', 'set<string>'),
        'objectType' => array('The keyword identifying the type of asset this is.', 'string'),
        'groups' => array('T<Deprecated> An array of strings containing the M<id> URI of the O<Group> object that this asset is mapped into, if any. This property has been superseded by the M<container> property.', 'array<string>'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of this asset. Only the one object type URI for the particular type of asset this asset is will be present.', 'set<string>'),
        'container' => array('An object describing the group or blog to which this asset belongs.', 'ContainerRef'),
        'description' => array('The description of the asset.', 'string'),
        'commentCount' => array('The number of comments that have been posted in reply to this asset. This number includes comments that have been posted in response to other comments.', 'integer'),
        'published' => array('The time at which the asset was created, as a W3CDTF timestamp.', 'string'),
        'textFormat' => array('A keyword that indicates what formatting mode to use for the content of this asset. This can be C<html> for assets the content of which is HTML, C<html_convert_linebreaks> for assets the content of which is HTML but where paragraph tags should be added automatically, or C<markdown> for assets the content of which is Markdown source. Other formatting modes may be added in future. Applications that present assets for editing should use this property to present an appropriate editor.', 'string'),
        'permalinkUrl' => array('The URL that is this asset\'s permalink. This will be omitted if the asset does not have a permalink of its own (for example, if it\'s embedded in another asset) or if TypePad does not know its permalink.', 'string'),
        'title' => array('The title of the asset.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'AssetSource');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->container) && (get_class($data->container) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->container->objectType) ? $data->container->objectType : 'ContainerRef');
            $this->container = new $ot_class($data->container);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
        if (isset($data->audioLink) && (get_class($data->audioLink) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->audioLink->objectType) ? $data->audioLink->objectType : 'AudioLink');
            $this->audioLink = new $ot_class($data->audioLink);
        }
        if (isset($data->publicationStatus) && (get_class($data->publicationStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->publicationStatus->objectType) ? $data->publicationStatus->objectType : 'PublicationStatus');
            $this->publicationStatus = new $ot_class($data->publicationStatus);
        }
    }
}

class TPComment extends TPAsset {

    protected static $properties = array(
        'source' => array('An object describing the site from which this asset was retrieved, if the asset was obtained from an external source.', 'AssetSource'),
        'excerpt' => array('A short, plain-text excerpt of the entry content. This is currently available only for O<Post> assets.', 'string'),
        'content' => array('The raw asset content. The M<textFormat> property describes how to format this data. Use this property to set the asset content in write operations. An asset posted in a group may have a M<content> value up to 10,000 bytes long, while a O<Post> asset in a blog may have up to 65,000 bytes of content.', 'string'),
        'favoriteCount' => array('The number of distinct users who have added this asset as a favorite.', 'integer'),
        'author' => array('The user who created the selected asset.', 'User'),
        'isFavoriteForCurrentUser' => array('C<true> if this asset is a favorite for the currently authenticated user, or C<false> otherwise. This property is omitted from responses to anonymous requests.', 'boolean'),
        'publicationStatus' => array('T<Editable> An object describing the visibility status and publication date for this page. Only visibility status is editable.', 'PublicationStatus'),
        'renderedContent' => array('The content of this asset rendered to HTML. This is currently available only for O<Post> and O<Page> assets.', 'string'),
        'crosspostAccounts' => array('T<Editable> A set of identifiers for O<Account> objects to which to crosspost this asset when it\'s posted. This property is omitted when retrieving existing assets.', 'set<string>'),
        'objectType' => array('The keyword identifying the type of asset this is.', 'string'),
        'groups' => array('T<Deprecated> An array of strings containing the M<id> URI of the O<Group> object that this asset is mapped into, if any. This property has been superseded by the M<container> property.', 'array<string>'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'inReplyTo' => array('A reference to the asset that this comment is in reply to.', 'AssetRef'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of this asset. Only the one object type URI for the particular type of asset this asset is will be present.', 'set<string>'),
        'container' => array('An object describing the group or blog to which this asset belongs.', 'ContainerRef'),
        'description' => array('The description of the asset.', 'string'),
        'commentCount' => array('The number of comments that have been posted in reply to this asset. This number includes comments that have been posted in response to other comments.', 'integer'),
        'published' => array('The time at which the asset was created, as a W3CDTF timestamp.', 'string'),
        'textFormat' => array('A keyword that indicates what formatting mode to use for the content of this asset. This can be C<html> for assets the content of which is HTML, C<html_convert_linebreaks> for assets the content of which is HTML but where paragraph tags should be added automatically, or C<markdown> for assets the content of which is Markdown source. Other formatting modes may be added in future. Applications that present assets for editing should use this property to present an appropriate editor.', 'string'),
        'permalinkUrl' => array('The URL that is this asset\'s permalink. This will be omitted if the asset does not have a permalink of its own (for example, if it\'s embedded in another asset) or if TypePad does not know its permalink.', 'string'),
        'title' => array('The title of the asset.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'AssetSource');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->inReplyTo) && (get_class($data->inReplyTo) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->inReplyTo->objectType) ? $data->inReplyTo->objectType : 'AssetRef');
            $this->inReplyTo = new $ot_class($data->inReplyTo);
        }
        if (isset($data->container) && (get_class($data->container) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->container->objectType) ? $data->container->objectType : 'ContainerRef');
            $this->container = new $ot_class($data->container);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
        if (isset($data->publicationStatus) && (get_class($data->publicationStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->publicationStatus->objectType) ? $data->publicationStatus->objectType : 'PublicationStatus');
            $this->publicationStatus = new $ot_class($data->publicationStatus);
        }
    }
}

class TPLink extends TPAsset {

    protected static $properties = array(
        'source' => array('An object describing the site from which this asset was retrieved, if the asset was obtained from an external source.', 'AssetSource'),
        'excerpt' => array('A short, plain-text excerpt of the entry content. This is currently available only for O<Post> assets.', 'string'),
        'content' => array('The raw asset content. The M<textFormat> property describes how to format this data. Use this property to set the asset content in write operations. An asset posted in a group may have a M<content> value up to 10,000 bytes long, while a O<Post> asset in a blog may have up to 65,000 bytes of content.', 'string'),
        'favoriteCount' => array('The number of distinct users who have added this asset as a favorite.', 'integer'),
        'author' => array('The user who created the selected asset.', 'User'),
        'isFavoriteForCurrentUser' => array('C<true> if this asset is a favorite for the currently authenticated user, or C<false> otherwise. This property is omitted from responses to anonymous requests.', 'boolean'),
        'publicationStatus' => array('T<Editable> An object describing the visibility status and publication date for this asset. Only visibility status is editable.', 'PublicationStatus'),
        'renderedContent' => array('The content of this asset rendered to HTML. This is currently available only for O<Post> and O<Page> assets.', 'string'),
        'crosspostAccounts' => array('T<Editable> A set of identifiers for O<Account> objects to which to crosspost this asset when it\'s posted. This property is omitted when retrieving existing assets.', 'set<string>'),
        'objectType' => array('The keyword identifying the type of asset this is.', 'string'),
        'groups' => array('T<Deprecated> An array of strings containing the M<id> URI of the O<Group> object that this asset is mapped into, if any. This property has been superseded by the M<container> property.', 'array<string>'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of this asset. Only the one object type URI for the particular type of asset this asset is will be present.', 'set<string>'),
        'container' => array('An object describing the group or blog to which this asset belongs.', 'ContainerRef'),
        'description' => array('The description of the asset.', 'string'),
        'commentCount' => array('The number of comments that have been posted in reply to this asset. This number includes comments that have been posted in response to other comments.', 'integer'),
        'published' => array('The time at which the asset was created, as a W3CDTF timestamp.', 'string'),
        'targetUrl' => array('The URL that is the target of this link.', 'string'),
        'textFormat' => array('A keyword that indicates what formatting mode to use for the content of this asset. This can be C<html> for assets the content of which is HTML, C<html_convert_linebreaks> for assets the content of which is HTML but where paragraph tags should be added automatically, or C<markdown> for assets the content of which is Markdown source. Other formatting modes may be added in future. Applications that present assets for editing should use this property to present an appropriate editor.', 'string'),
        'permalinkUrl' => array('The URL that is this asset\'s permalink. This will be omitted if the asset does not have a permalink of its own (for example, if it\'s embedded in another asset) or if TypePad does not know its permalink.', 'string'),
        'title' => array('The title of the asset.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'AssetSource');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->container) && (get_class($data->container) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->container->objectType) ? $data->container->objectType : 'ContainerRef');
            $this->container = new $ot_class($data->container);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
        if (isset($data->publicationStatus) && (get_class($data->publicationStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->publicationStatus->objectType) ? $data->publicationStatus->objectType : 'PublicationStatus');
            $this->publicationStatus = new $ot_class($data->publicationStatus);
        }
    }
}

class TPPage extends TPAsset {

    protected static $properties = array(
        'source' => array('An object describing the site from which this asset was retrieved, if the asset was obtained from an external source.', 'AssetSource'),
        'excerpt' => array('A short, plain-text excerpt of the entry content. This is currently available only for O<Post> assets.', 'string'),
        'content' => array('The raw asset content. The M<textFormat> property describes how to format this data. Use this property to set the asset content in write operations. An asset posted in a group may have a M<content> value up to 10,000 bytes long, while a O<Post> asset in a blog may have up to 65,000 bytes of content.', 'string'),
        'favoriteCount' => array('The number of distinct users who have added this asset as a favorite.', 'integer'),
        'author' => array('The user who created the selected asset.', 'User'),
        'isFavoriteForCurrentUser' => array('C<true> if this asset is a favorite for the currently authenticated user, or C<false> otherwise. This property is omitted from responses to anonymous requests.', 'boolean'),
        'publicationStatus' => array('T<Editable> An object describing the draft status and publication date for this page.', 'PublicationStatus'),
        'renderedContent' => array('The content of this asset rendered to HTML. This is currently available only for O<Post> and O<Page> assets.', 'string'),
        'crosspostAccounts' => array('T<Editable> A set of identifiers for O<Account> objects to which to crosspost this asset when it\'s posted. This property is omitted when retrieving existing assets.', 'set<string>'),
        'objectType' => array('The keyword identifying the type of asset this is.', 'string'),
        'feedbackStatus' => array('T<Editable> An object describing the comment and trackback behavior for this page.', 'FeedbackStatus'),
        'groups' => array('T<Deprecated> An array of strings containing the M<id> URI of the O<Group> object that this asset is mapped into, if any. This property has been superseded by the M<container> property.', 'array<string>'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of this asset. Only the one object type URI for the particular type of asset this asset is will be present.', 'set<string>'),
        'container' => array('An object describing the group or blog to which this asset belongs.', 'ContainerRef'),
        'description' => array('T<Editable> The description of the page.', 'string'),
        'commentCount' => array('The number of comments that have been posted in reply to this asset. This number includes comments that have been posted in response to other comments.', 'integer'),
        'published' => array('The time at which the asset was created, as a W3CDTF timestamp.', 'string'),
        'permalinkUrl' => array('The URL that is this asset\'s permalink. This will be omitted if the asset does not have a permalink of its own (for example, if it\'s embedded in another asset) or if TypePad does not know its permalink.', 'string'),
        'textFormat' => array('T<Editable> A keyword that indicates what formatting mode to use for the content of this page. This can be C<html> for assets the content of which is HTML, C<html_convert_linebreaks> for assets the content of which is HTML but where paragraph tags should be added automatically, or C<markdown> for assets the content of which is Markdown source. Other formatting modes may be added in future. Applications that present assets for editing should use this property to present an appropriate editor.', 'string'),
        'embeddedImageLinks' => array('A list of links to the images that are embedded within the content of this page.', 'array<ImageLink>'),
        'filename' => array('T<Editable> The base name of the page, used to create the M<permalinkUrl>.', 'string'),
        'title' => array('T<Editable> The title of the page.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'AssetSource');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->embeddedImageLinks)) $this->embeddedImageLinks = new TPList($data->embeddedImageLinks, 'TPImageLink');
        if (isset($data->feedbackStatus) && (get_class($data->feedbackStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->feedbackStatus->objectType) ? $data->feedbackStatus->objectType : 'FeedbackStatus');
            $this->feedbackStatus = new $ot_class($data->feedbackStatus);
        }
        if (isset($data->container) && (get_class($data->container) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->container->objectType) ? $data->container->objectType : 'ContainerRef');
            $this->container = new $ot_class($data->container);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
        if (isset($data->publicationStatus) && (get_class($data->publicationStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->publicationStatus->objectType) ? $data->publicationStatus->objectType : 'PublicationStatus');
            $this->publicationStatus = new $ot_class($data->publicationStatus);
        }
    }
}

class TPPhoto extends TPAsset {

    protected static $properties = array(
        'source' => array('An object describing the site from which this asset was retrieved, if the asset was obtained from an external source.', 'AssetSource'),
        'excerpt' => array('A short, plain-text excerpt of the entry content. This is currently available only for O<Post> assets.', 'string'),
        'content' => array('The raw asset content. The M<textFormat> property describes how to format this data. Use this property to set the asset content in write operations. An asset posted in a group may have a M<content> value up to 10,000 bytes long, while a O<Post> asset in a blog may have up to 65,000 bytes of content.', 'string'),
        'favoriteCount' => array('The number of distinct users who have added this asset as a favorite.', 'integer'),
        'author' => array('The user who created the selected asset.', 'User'),
        'isFavoriteForCurrentUser' => array('C<true> if this asset is a favorite for the currently authenticated user, or C<false> otherwise. This property is omitted from responses to anonymous requests.', 'boolean'),
        'publicationStatus' => array('T<Editable> An object describing the visibility status and publication date for this asset. Only visibility status is editable.', 'PublicationStatus'),
        'renderedContent' => array('The content of this asset rendered to HTML. This is currently available only for O<Post> and O<Page> assets.', 'string'),
        'crosspostAccounts' => array('T<Editable> A set of identifiers for O<Account> objects to which to crosspost this asset when it\'s posted. This property is omitted when retrieving existing assets.', 'set<string>'),
        'objectType' => array('The keyword identifying the type of asset this is.', 'string'),
        'groups' => array('T<Deprecated> An array of strings containing the M<id> URI of the O<Group> object that this asset is mapped into, if any. This property has been superseded by the M<container> property.', 'array<string>'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of this asset. Only the one object type URI for the particular type of asset this asset is will be present.', 'set<string>'),
        'container' => array('An object describing the group or blog to which this asset belongs.', 'ContainerRef'),
        'description' => array('The description of the asset.', 'string'),
        'commentCount' => array('The number of comments that have been posted in reply to this asset. This number includes comments that have been posted in response to other comments.', 'integer'),
        'published' => array('The time at which the asset was created, as a W3CDTF timestamp.', 'string'),
        'textFormat' => array('A keyword that indicates what formatting mode to use for the content of this asset. This can be C<html> for assets the content of which is HTML, C<html_convert_linebreaks> for assets the content of which is HTML but where paragraph tags should be added automatically, or C<markdown> for assets the content of which is Markdown source. Other formatting modes may be added in future. Applications that present assets for editing should use this property to present an appropriate editor.', 'string'),
        'permalinkUrl' => array('The URL that is this asset\'s permalink. This will be omitted if the asset does not have a permalink of its own (for example, if it\'s embedded in another asset) or if TypePad does not know its permalink.', 'string'),
        'title' => array('The title of the asset.', 'string'),
        'imageLink' => array('A link to the image that is this Photo asset\'s content.', 'ImageLink')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'AssetSource');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->container) && (get_class($data->container) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->container->objectType) ? $data->container->objectType : 'ContainerRef');
            $this->container = new $ot_class($data->container);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
        if (isset($data->imageLink) && (get_class($data->imageLink) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->imageLink->objectType) ? $data->imageLink->objectType : 'ImageLink');
            $this->imageLink = new $ot_class($data->imageLink);
        }
        if (isset($data->publicationStatus) && (get_class($data->publicationStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->publicationStatus->objectType) ? $data->publicationStatus->objectType : 'PublicationStatus');
            $this->publicationStatus = new $ot_class($data->publicationStatus);
        }
    }
}

class TPPost extends TPAsset {

    protected static $properties = array(
        'source' => array('An object describing the site from which this asset was retrieved, if the asset was obtained from an external source.', 'AssetSource'),
        'excerpt' => array('A short, plain-text excerpt of the entry content. This is currently available only for O<Post> assets.', 'string'),
        'embeddedAudioLinks' => array('A list of links to the audio streams that are embedded within the content of this post.', 'array<AudioLink>'),
        'content' => array('T<Editable> The raw post content. The M<textFormat> property defines what format this data is in.', 'string'),
        'favoriteCount' => array('The number of distinct users who have added this asset as a favorite.', 'integer'),
        'author' => array('The user who created the selected asset.', 'User'),
        'reblogCount' => array('The number of times this post has been reblogged by other people.', 'integer'),
        'isFavoriteForCurrentUser' => array('C<true> if this asset is a favorite for the currently authenticated user, or C<false> otherwise. This property is omitted from responses to anonymous requests.', 'boolean'),
        'publicationStatus' => array('T<Editable> An object describing the draft status and publication date for this post.', 'PublicationStatus'),
        'renderedContent' => array('The content of this asset rendered to HTML. This is currently available only for O<Post> and O<Page> assets.', 'string'),
        'crosspostAccounts' => array('T<Editable> A set of identifiers for O<Account> objects to which to crosspost this asset when it\'s posted. This property is omitted when retrieving existing assets.', 'set<string>'),
        'objectType' => array('The keyword identifying the type of asset this is.', 'string'),
        'feedbackStatus' => array('T<Editable> An object describing the comment and trackback behavior for this post.', 'FeedbackStatus'),
        'groups' => array('T<Deprecated> An array of strings containing the M<id> URI of the O<Group> object that this asset is mapped into, if any. This property has been superseded by the M<container> property.', 'array<string>'),
        'reblogOf' => array('A reference to a post of which this post is a reblog.', 'AssetRef'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'embeddedVideoLinks' => array('A list of links to the videos that are embedded within the content of this post.', 'array<VideoLink>'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of this asset. Only the one object type URI for the particular type of asset this asset is will be present.', 'set<string>'),
        'container' => array('An object describing the group or blog to which this asset belongs.', 'ContainerRef'),
        'categories' => array('T<Editable> A list of categories associated with the post.', 'array<string>'),
        'description' => array('T<Editable> The description of the post.', 'string'),
        'commentCount' => array('The number of comments that have been posted in reply to this asset. This number includes comments that have been posted in response to other comments.', 'integer'),
        'published' => array('The time at which the asset was created, as a W3CDTF timestamp.', 'string'),
        'permalinkUrl' => array('The URL that is this asset\'s permalink. This will be omitted if the asset does not have a permalink of its own (for example, if it\'s embedded in another asset) or if TypePad does not know its permalink.', 'string'),
        'textFormat' => array('T<Editable> A keyword that indicates what formatting mode to use for the content of this post. This can be C<html> for assets the content of which is HTML, C<html_convert_linebreaks> for assets the content of which is HTML but where paragraph tags should be added automatically, or C<markdown> for assets the content of which is Markdown source. Other formatting modes may be added in future. Applications that present assets for editing should use this property to present an appropriate editor.', 'string'),
        'filename' => array('T<Editable> The base name of the post to use when creating its M<permalinkUrl>.', 'string'),
        'embeddedImageLinks' => array('A list of links to the images that are embedded within the content of this post.', 'array<ImageLink>'),
        'title' => array('T<Editable> The title of the post.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'AssetSource');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->embeddedVideoLinks)) $this->embeddedVideoLinks = new TPList($data->embeddedVideoLinks, 'TPVideoLink');
        if (isset($data->embeddedAudioLinks)) $this->embeddedAudioLinks = new TPList($data->embeddedAudioLinks, 'TPAudioLink');
        if (isset($data->container) && (get_class($data->container) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->container->objectType) ? $data->container->objectType : 'ContainerRef');
            $this->container = new $ot_class($data->container);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
        if (isset($data->publicationStatus) && (get_class($data->publicationStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->publicationStatus->objectType) ? $data->publicationStatus->objectType : 'PublicationStatus');
            $this->publicationStatus = new $ot_class($data->publicationStatus);
        }
        if (isset($data->embeddedImageLinks)) $this->embeddedImageLinks = new TPList($data->embeddedImageLinks, 'TPImageLink');
        if (isset($data->feedbackStatus) && (get_class($data->feedbackStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->feedbackStatus->objectType) ? $data->feedbackStatus->objectType : 'FeedbackStatus');
            $this->feedbackStatus = new $ot_class($data->feedbackStatus);
        }
        if (isset($data->reblogOf) && (get_class($data->reblogOf) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->reblogOf->objectType) ? $data->reblogOf->objectType : 'AssetRef');
            $this->reblogOf = new $ot_class($data->reblogOf);
        }
    }
}

class TPVideo extends TPAsset {

    protected static $properties = array(
        'source' => array('An object describing the site from which this asset was retrieved, if the asset was obtained from an external source.', 'AssetSource'),
        'excerpt' => array('A short, plain-text excerpt of the entry content. This is currently available only for O<Post> assets.', 'string'),
        'videoLink' => array('A link to the video that is this Video asset\'s content.', 'VideoLink'),
        'content' => array('The raw asset content. The M<textFormat> property describes how to format this data. Use this property to set the asset content in write operations. An asset posted in a group may have a M<content> value up to 10,000 bytes long, while a O<Post> asset in a blog may have up to 65,000 bytes of content.', 'string'),
        'favoriteCount' => array('The number of distinct users who have added this asset as a favorite.', 'integer'),
        'author' => array('The user who created the selected asset.', 'User'),
        'isFavoriteForCurrentUser' => array('C<true> if this asset is a favorite for the currently authenticated user, or C<false> otherwise. This property is omitted from responses to anonymous requests.', 'boolean'),
        'publicationStatus' => array('T<Editable> An object describing the visibility status and publication date for this asset. Only visibility status is editable.', 'PublicationStatus'),
        'renderedContent' => array('The content of this asset rendered to HTML. This is currently available only for O<Post> and O<Page> assets.', 'string'),
        'crosspostAccounts' => array('T<Editable> A set of identifiers for O<Account> objects to which to crosspost this asset when it\'s posted. This property is omitted when retrieving existing assets.', 'set<string>'),
        'objectType' => array('The keyword identifying the type of asset this is.', 'string'),
        'groups' => array('T<Deprecated> An array of strings containing the M<id> URI of the O<Group> object that this asset is mapped into, if any. This property has been superseded by the M<container> property.', 'array<string>'),
        'previewImageLink' => array('A link to a preview image or poster frame for this video. This property is omitted if no such image is available.', 'ImageLink'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of this asset. Only the one object type URI for the particular type of asset this asset is will be present.', 'set<string>'),
        'container' => array('An object describing the group or blog to which this asset belongs.', 'ContainerRef'),
        'description' => array('The description of the asset.', 'string'),
        'commentCount' => array('The number of comments that have been posted in reply to this asset. This number includes comments that have been posted in response to other comments.', 'integer'),
        'published' => array('The time at which the asset was created, as a W3CDTF timestamp.', 'string'),
        'textFormat' => array('A keyword that indicates what formatting mode to use for the content of this asset. This can be C<html> for assets the content of which is HTML, C<html_convert_linebreaks> for assets the content of which is HTML but where paragraph tags should be added automatically, or C<markdown> for assets the content of which is Markdown source. Other formatting modes may be added in future. Applications that present assets for editing should use this property to present an appropriate editor.', 'string'),
        'permalinkUrl' => array('The URL that is this asset\'s permalink. This will be omitted if the asset does not have a permalink of its own (for example, if it\'s embedded in another asset) or if TypePad does not know its permalink.', 'string'),
        'title' => array('The title of the asset.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'AssetSource');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->videoLink) && (get_class($data->videoLink) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->videoLink->objectType) ? $data->videoLink->objectType : 'VideoLink');
            $this->videoLink = new $ot_class($data->videoLink);
        }
        if (isset($data->container) && (get_class($data->container) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->container->objectType) ? $data->container->objectType : 'ContainerRef');
            $this->container = new $ot_class($data->container);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
        if (isset($data->publicationStatus) && (get_class($data->publicationStatus) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->publicationStatus->objectType) ? $data->publicationStatus->objectType : 'PublicationStatus');
            $this->publicationStatus = new $ot_class($data->publicationStatus);
        }
        if (isset($data->previewImageLink) && (get_class($data->previewImageLink) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->previewImageLink->objectType) ? $data->previewImageLink->objectType : 'ImageLink');
            $this->previewImageLink = new $ot_class($data->previewImageLink);
        }
    }
}

class TPAssetExtendedContent extends TPBase {

    protected static $properties = array(
        'renderedExtendedContent' => array('The HTML rendered version of this asset\'s extended content, if it has any. Otherwise, this property is omitted.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPAssetRef extends TPBase {

    protected static $properties = array(
        'author' => array('The user who created the referenced asset.', 'User'),
        'objectType' => array('The keyword identifying the type of asset the referenced O<Asset> object is.', 'string'),
        'href' => array('The URL of a representation of the referenced asset.', 'string'),
        'id' => array('The URI from the referenced O<Asset> object\'s M<id> property.', 'string'),
        'urlId' => array('The canonical identifier from the referenced O<Asset> object\'s M<urlId> property.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs identifying the type of the referenced asset. Only the one object type URI for the particular type of asset the referenced asset is will be present.', 'array<string>'),
        'type' => array('The MIME type of the representation at the URL given in the M<href> property.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
    }
}

class TPAssetSource extends TPBase {

    protected static $properties = array(
        'provider' => array('T<Deprecated> Description of the external service provider from which this content was imported, if known. Contains C<name>, C<icon>, and C<uri> properties. This property will be omitted if the service from which the related asset was imported is not recognized.', 'map<string>'),
        'byUser' => array('T<Deprecated> C<true> if this content is considered to be created by its author, or C<false> if it\'s actually someone else\'s content imported by the asset author.', 'boolean'),
        'permalinkUrl' => array('The permalink URL of the resource from which the related asset was imported.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPAudioLink extends TPBase {

    protected static $properties = array(
        'url' => array('The URL of an MP3 representation of the audio stream.', 'string'),
        'duration' => array('The duration of the audio stream in seconds. This property will be omitted if the length of the audio stream could not be determined.', 'integer')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPAuthToken extends TPBase {

    protected static $properties = array(
        'targetObject' => array('T<Deprecated> The root object to which this auth token grants access. This is a legacy field maintained for backwards compatibility with older clients, as auth tokens are no longer scoped to specific objects.', 'Base'),
        'authToken' => array('The actual auth token string. Use this as the access token when making an OAuth request.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->targetObject) && (get_class($data->targetObject) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->targetObject->objectType) ? $data->targetObject->objectType : 'Base');
            $this->targetObject = new $ot_class($data->targetObject);
        }
    }
}

class TPBadge extends TPBase {

    protected static $properties = array(
        'id' => array('The canonical identifier that can be used to identify this badge in URLs.  This can be used to recognise where the same badge is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'displayName' => array('A human-readable name for this badge.', 'string'),
        'description' => array('A human-readable description of what a user must do to win this badge.', 'string'),
        'imageLink' => array('A link to the image that depicts this badge to users.', 'ImageLink')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->imageLink) && (get_class($data->imageLink) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->imageLink->objectType) ? $data->imageLink->objectType : 'ImageLink');
            $this->imageLink = new $ot_class($data->imageLink);
        }
    }
}

class TPBlog extends TPBase {

    protected static $properties = array(
        'objectType' => array('The keyword identifying the type of object this is. For a Blog object, M<objectType> will be C<Blog>.', 'string'),
        'id' => array('A URI that serves as a globally unique identifier for the object.', 'string'),
        'owner' => array('The user who owns the blog.', 'User'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs. This set will contain the string C<tag:api.typepad.com,2009:Blog> for a Blog object.', 'set<string>'),
        'description' => array('The description of the blog as provided by its owner.', 'string'),
        'title' => array('The title of the blog.', 'string'),
        'homeUrl' => array('The URL of the blog\'s home page.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->owner) && (get_class($data->owner) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->owner->objectType) ? $data->owner->objectType : 'User');
            $this->owner = new $ot_class($data->owner);
        }
    }
}

class TPBlogCommentingSettings extends TPBase {

    protected static $properties = array(
        'signinRequired' => array('C<true> if this blog requires users to be logged in in order to leave a comment, or C<false> if anonymous comments will be rejected.', 'boolean'),
        'moderationEnabled' => array('C<true> if this blog places new comments into a moderation queue for approval before they are displayed, or C<false> if new comments may be available immediately.', 'boolean'),
        'emailAddressRequired' => array('C<true> if this blog requires anonymous comments to be submitted with an email address, or C<false> otherwise.', 'boolean'),
        'signinAllowed' => array('C<true> if this blog allows users to sign in to comment, or C<false> if all new comments are anonymous.', 'boolean'),
        'htmlAllowed' => array('C<true> if this blog allows commenters to use basic HTML formatting in comments, or C<false> if HTML will be removed.', 'boolean'),
        'urlsAutoLinked' => array('C<true> if comments in this blog will automatically have any bare URLs turned into links, or C<false> if URLs will be shown unlinked.', 'boolean'),
        'captchaRequired' => array('C<true> if this blog requires anonymous commenters to pass a CAPTCHA before submitting a comment, or C<false> otherwise.', 'boolean'),
        'timeLimit' => array('Number of days after a post is published that comments will be allowed. If the blog has no time limit for comments, this property will be omitted.', 'integer')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPBlogStats extends TPBase {

    protected static $properties = array(
        'totalPageViews' => array('The total number of page views received by the blog for all time.', 'integer'),
        'dailyPageViews' => array('A map containing the daily page views on the blog for the last 120 days. The keys of the map are dates in W3CDTF format, and the values are the integer number of page views on the blog for that date.', 'map<integer>')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPCommentTreeItem extends TPBase {

    protected static $properties = array(
        'depth' => array('The number of levels deep this comment is in the tree. A comment that is directly in reply to the root asset is 1 level deep. If a given comment has a depth of 1, all of the direct replies to that comment will have a depth of 2; their replies will have depth 3, and so forth.', 'integer'),
        'comment' => array('The comment asset at this point in the tree.', 'Asset')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->comment) && (get_class($data->comment) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->comment->objectType) ? $data->comment->objectType : 'Asset');
            $this->comment = new $ot_class($data->comment);
        }
    }
}

class TPContainerRef extends TPBase {

    protected static $properties = array(
        'objectType' => array('The keyword identifying the type of object the referenced container is.', 'string'),
        'id' => array('The URI from the M<id> property of the referenced blog or group.', 'string'),
        'displayName' => array('The display name of the blog or group, as set by its owner.', 'string'),
        'urlId' => array('The canonical identifier from the M<urlId> property of the referenced blog or group.', 'string'),
        'homeUrl' => array('The URL of the home page of the referenced blog or group.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPEndpoint extends TPBase {

    protected static $properties = array(
        'canHaveId' => array('For noun endpoints, C<true> if an id part is accepted, or C<false> if the noun may only be used alone.', 'boolean'),
        'supportedMethods' => array('A mapping of the HTTP methods that this endpoint accepts to the docstrings describing the result of each method.', 'map<string>'),
        'postObjectType' => array('The type of object that this endpoint accepts for C<POST> operations. This property is omitted if this endpoint does not accept C<POST> requests.', 'ObjectType'),
        'resourceObjectType' => array('The type of object that this endpoint represents for C<GET>, C<PUT> and C<DELETE> operations. This property is omitted for action endpoints, as they do not represent resources.', 'ObjectType'),
        'formatSensitive' => array('C<true> if this endpoint requires a format suffix, or C<false> otherwise.', 'boolean'),
        'canOmitId' => array('For noun endpoints, C<true> if the id part can be ommitted, or C<false> if it is always required.', 'boolean'),
        'parameterized' => array('For filter endpoints, C<true> if a parameter is required on the filter, or C<false> if it\'s a boolean filter.', 'boolean'),
        'name' => array('The name of the endpoint, as it appears in URLs.', 'string'),
        'responseObjectType' => array('For action endpoints, the type of object that this endpoint returns on success. If the endpoint returns no payload on success, or if this is not an action endpoint, this property is omitted.', 'ObjectType'),
        'supportedQueryArguments' => array('The names of the query string arguments that this endpoint accepts.', 'set<string>'),
        'propertyEndpoints' => array('For noun endpoints, an array of property endpoints that it supports.', 'array<Endpoint>'),
        'actionEndpoints' => array('For noun endpoints, an array of action endpoints that it supports.', 'array<Endpoint>'),
        'filterEndpoints' => array('For endpoints that return lists, an array of filters that can be appended to the endpoint.', 'array<Endpoint>')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->resourceObjectType) && (get_class($data->resourceObjectType) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->resourceObjectType->objectType) ? $data->resourceObjectType->objectType : 'ObjectType');
            $this->resourceObjectType = new $ot_class($data->resourceObjectType);
        }
        if (isset($data->propertyEndpoints)) $this->propertyEndpoints = new TPList($data->propertyEndpoints, 'TPEndpoint');
        if (isset($data->actionEndpoints)) $this->actionEndpoints = new TPList($data->actionEndpoints, 'TPEndpoint');
        if (isset($data->postObjectType) && (get_class($data->postObjectType) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->postObjectType->objectType) ? $data->postObjectType->objectType : 'ObjectType');
            $this->postObjectType = new $ot_class($data->postObjectType);
        }
        if (isset($data->filterEndpoints)) $this->filterEndpoints = new TPList($data->filterEndpoints, 'TPEndpoint');
        if (isset($data->responseObjectType) && (get_class($data->responseObjectType) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->responseObjectType->objectType) ? $data->responseObjectType->objectType : 'ObjectType');
            $this->responseObjectType = new $ot_class($data->responseObjectType);
        }
    }
}

class TPEntity extends TPBase {

    protected static $properties = array(
        'id' => array('A URI that serves as a globally unique identifier for the object.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return true; }

}

class TPApplication extends TPEntity {

    protected static $properties = array(
        'sessionSyncScriptUrl' => array('The URL of the session sync script.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'oauthIdentificationUrl' => array('The URL to send the user\'s browser to in order to identify who is logged in (that is, the "sign in" link).', 'string'),
        'objectTypes' => array('T<Deprecated> The object types for this object. This set will contain the string C<tag:api.typepad.com,2009:Application> for an Application object.', 'set<string>'),
        'name' => array('The name of the application as provided by its developer.', 'string'),
        'oauthAuthorizationUrl' => array('The URL to send the user\'s browser to for the user authorization step.', 'string'),
        'signoutUrl' => array('The URL to send the user\'s browser to in order to sign them out of TypePad.', 'string'),
        'userFlyoutsScriptUrl' => array('The URL of a script to embed to enable the user flyouts functionality.', 'string'),
        'objectType' => array('The keyword identifying the type of object this is. For an Application object, M<objectType> will be C<Application>.', 'string'),
        'oauthRequestTokenUrl' => array('The URL of the OAuth request token endpoint for this application.', 'string'),
        'id' => array('A string containing the canonical identifier that can be used to identify this application in URLs.', 'string'),
        'oauthAccessTokenUrl' => array('The URL of the OAuth access token endpoint for this application.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPGroup extends TPEntity {

    protected static $properties = array(
        'objectType' => array('A keyword describing the type of this object. For a group object, M<objectType> will be C<Group>.', 'string'),
        'id' => array('A URI that serves as a globally unique identifier for the object.', 'string'),
        'avatarLink' => array('A link to an image representing this group.', 'ImageLink'),
        'displayName' => array('The display name set by the group\'s owner.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs.', 'set<string>'),
        'siteUrl' => array('The URL to the front page of the group website.', 'string'),
        'tagline' => array('A tagline describing the group, as set by the group\'s owner.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->avatarLink) && (get_class($data->avatarLink) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->avatarLink->objectType) ? $data->avatarLink->objectType : 'ImageLink');
            $this->avatarLink = new $ot_class($data->avatarLink);
        }
    }
}

class TPUser extends TPEntity {

    protected static $properties = array(
        'email' => array('T<Deprecated> The user\'s email address. This property is only provided for authenticated requests if the user has shared it with the authenticated application, and the authenticated user is allowed to view it (as with administrators of groups the user has joined). In all other cases, this property is omitted.', 'string'),
        'objectType' => array('The keyword identifying the type of object this is. For a User object, M<objectType> will be C<User>.', 'string'),
        'preferredUsername' => array('The name the user has chosen for use in the URL of their TypePad profile page. This property can be used to select this user in URLs, although it is not a persistent key, as the user can change it at any time.', 'string'),
        'id' => array('A URI that serves as a globally unique identifier for the object.', 'string'),
        'avatarLink' => array('A link to an image representing this user.', 'ImageLink'),
        'gender' => array('T<Deprecated> The user\'s gender, as they provided it. This property is only provided for authenticated requests if the user has shared it with the authenticated application, and the authenticated user is allowed to view it (as with administrators of groups the user has joined). In all other cases, this property is omitted.', 'string'),
        'displayName' => array('The user\'s chosen display name.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'interests' => array('T<Deprecated> A list of interests provided by the user and displayed on the user\'s profile page. Use the M<interests> property of the O<UserProfile> object, which can be retrieved from the N</users/{id}/profile> endpoint.', 'array<string>'),
        'location' => array('T<Deprecated> The user\'s location, as a free-form string provided by them. Use the the M<location> property of the related O<UserProfile> object, which can be retrieved from the N</users/{id}/profile> endpoint.', 'string'),
        'objectTypes' => array('T<Deprecated> An array of object type identifier URIs.', 'set<string>'),
        'profilePageUrl' => array('The URL of the user\'s TypePad profile page.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->avatarLink) && (get_class($data->avatarLink) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->avatarLink->objectType) ? $data->avatarLink->objectType : 'ImageLink');
            $this->avatarLink = new $ot_class($data->avatarLink);
        }
    }
}

class TPEvent extends TPBase {

    protected static $properties = array(
        'object' => array('The object to which the action described by this event was performed.', 'Base'),
        'verbs' => array('T<Deprecated> An array of verb identifier URIs. This set will contain one verb identifier URI.', 'set<string>'),
        'verb' => array('A keyword identifying the type of event this is.', 'set<string>'),
        'id' => array('A URI that serves as a globally unique identifier for the user.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'actor' => array('The user who performed the action described by this event.', 'Entity'),
        'published' => array('The time at which the event was performed, as a W3CDTF timestamp.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->object) && (get_class($data->object) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->object->objectType) ? $data->object->objectType : 'Base');
            $this->object = new $ot_class($data->object);
        }
        if (isset($data->actor) && (get_class($data->actor) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->actor->objectType) ? $data->actor->objectType : 'Entity');
            $this->actor = new $ot_class($data->actor);
        }
    }
}

class TPExternalFeedSubscription extends TPBase {

    protected static $properties = array(
        'filterRules' => array('A list of rules for filtering notifications to this subscription. Each rule is a full-text search query string, like those used with the N</assets> endpoint. An item will be delivered to the M<callbackUrl> if it matches any one of these query strings.', 'array<string>'),
        'postAsUserId' => array('For a Group-owned subscription, the urlId of the User who will own the items posted into the group by the subscription.', 'array<string>'),
        'callbackStatus' => array('The HTTP status code that was returned by the last call to the subscription\'s callback URL.', 'string'),
        'urlId' => array('The canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same user is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'callbackUrl' => array('The URL to which to send notifications of new items in this subscription\'s feeds.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPFavorite extends TPBase {

    protected static $properties = array(
        'author' => array('The user who saved this favorite. That is, this property is the user who saved the target asset as a favorite, not the creator of that asset.', 'User'),
        'id' => array('A URI that serves as a globally unique identifier for the favorite.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this favorite in URLs. This can be used to recognise where the same favorite is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'inReplyTo' => array('A reference to the target asset that has been marked as a favorite.', 'AssetRef'),
        'published' => array('The time that the favorite was created, as a W3CDTF timestamp.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->inReplyTo) && (get_class($data->inReplyTo) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->inReplyTo->objectType) ? $data->inReplyTo->objectType : 'AssetRef');
            $this->inReplyTo = new $ot_class($data->inReplyTo);
        }
        if (isset($data->author) && (get_class($data->author) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->author->objectType) ? $data->author->objectType : 'User');
            $this->author = new $ot_class($data->author);
        }
    }
}

class TPFeedbackStatus extends TPBase {

    protected static $properties = array(
        'allowComments' => array('C<true> if new comments may be posted to the related asset, or C<false> if no new comments are accepted.', 'boolean'),
        'allowTrackback' => array('C<true> if new trackback pings may be posted to the related asset, or C<false> if no new pings are accepted.', 'boolean'),
        'showComments' => array('C<true> if comments should be displayed on the related asset\'s permalink page, or C<false> if they should be hidden.', 'boolean')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPImageLink extends TPBase {

    protected static $properties = array(
        'width' => array('The width of the original image in pixels. If the width of the image is not available (for example, if the image isn\'t hosted on TypePad), this property will be omitted.', 'integer'),
        'url' => array('The URL for the original, full size version of the image.', 'string'),
        'height' => array('The height of the original image in pixels. If the height of the image is not available (for example, if the image isn\'t hosted on TypePad), this property will be omitted.', 'integer'),
        'urlTemplate' => array('An URL template with which to build alternate sizes of this image. If present, replace the placeholder string C<{spec}> with a valid sizing specifier to generate the URL for an alternate version of this image. This property is omitted if TypePad is unable to provide a scaled version of this image (for example, if the image isn\'t hosted on TypePad).', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPObjectProperty extends TPBase {

    protected static $properties = array(
        'name' => array('The name of the property.', 'string'),
        'docString' => array('A human-readable description of this property.', 'string'),
        'type' => array('The name of the type of this property.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPObjectType extends TPBase {

    protected static $properties = array(
        'name' => array('The name of this object type. If this is an anonymous type representing the request or response of an action endpoint, this property is omitted.', 'string'),
        'parentType' => array('The name of the parent type. This property is omitted if this object type has no parent type.', 'string'),
        'properties' => array('The properties belonging to objects of this object type.', 'array<ObjectProperty>')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->properties)) $this->properties = new TPList($data->properties, 'TPObjectProperty');
    }
}

class TPPostByEmailAddress extends TPBase {

    protected static $properties = array(
        'emailAddress' => array('A private email address for posting via email.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPPublicationStatus extends TPBase {

    protected static $properties = array(
        'publicationDate' => array('The time at which the related asset was (or will be) published, as a W3CDTF timestamp. If the related asset has been scheduled to be posted later, this property\'s timestamp will be in the future.', 'string'),
        'draft' => array('C<true> if this asset is private (not yet published), or C<false> if it has been published.', 'boolean')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPRelationshipStatus extends TPBase {

    protected static $properties = array(
        'types' => array('A list of relationship type URIs describing the types of the related relationship.', 'array<string>')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPUserBadge extends TPBase {

    protected static $properties = array(
        'badge' => array('The badge that was won.', 'Badge'),
        'earnedTime' => array('The time that the user earned the badge given in M<badge>.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->badge) && (get_class($data->badge) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->badge->objectType) ? $data->badge->objectType : 'Badge');
            $this->badge = new $ot_class($data->badge);
        }
    }
}

class TPUserProfile extends TPBase {

    protected static $properties = array(
        'profileEditPageUrl' => array('The URL of a page where this user can edit their profile information. If this is not the authenticated user\'s UserProfile object, this property is omitted.', 'string'),
        'followFrameContentUrl' => array('The URL of a widget that, when rendered in an C<iframe>, allows viewers to follow this user. Render this widget in an C<iframe> 300 pixels wide and 125 pixels high.', 'string'),
        'email' => array('The user\'s email address. This property is only provided for authenticated requests if the user has shared it with the authenticated application, and the authenticated user is allowed to view it (as with administrators of groups the user has joined). In all other cases, this property is omitted.', 'string'),
        'homepageUrl' => array('The address of the user\'s homepage, as a URL they provided. This property is omitted if the user has not provided a homepage.', 'string'),
        'preferredUsername' => array('The name the user has chosen for use in the URL of their TypePad profile page. This property can be used to select this user in URLs, although it is not a persistent key, as the user can change it at any time.', 'string'),
        'aboutMe' => array('The user\'s long description or biography, as a free-form string they provided.', 'string'),
        'id' => array('The URI from the related O<User> object\'s M<id> property.', 'string'),
        'avatarLink' => array('A link to an image representing this user.', 'ImageLink'),
        'gender' => array('The user\'s gender, as they provided it. This property is only provided for authenticated requests if the user has shared it with the authenticated application, and the authenticated user is allowed to view it (as with administrators of groups the user has joined). In all other cases, this property is omitted.', 'string'),
        'displayName' => array('The user\'s chosen display name.', 'string'),
        'urlId' => array('The canonical identifier from the related O<User> object\'s M<urlId> property.', 'string'),
        'interests' => array('A list of interests provided by the user and displayed on their profile page.', 'array<string>'),
        'location' => array('The user\'s location, as a free-form string they provided.', 'string'),
        'membershipManagementPageUrl' => array('The URL of a page where this user can manage their group memberships. If this is not the authenticated user\'s UserProfile object, this property is omitted.', 'string'),
        'profilePageUrl' => array('The URL of the user\'s TypePad profile page.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->avatarLink) && (get_class($data->avatarLink) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->avatarLink->objectType) ? $data->avatarLink->objectType : 'ImageLink');
            $this->avatarLink = new $ot_class($data->avatarLink);
        }
    }
}

class TPVideoLink extends TPBase {

    protected static $properties = array(
        'embedCode' => array('An opaque HTML fragment that, when embedded in a HTML page, provides an inline player for the video.', 'string'),
        'permalinkUrl' => array('T<Editable> The permalink URL for the video on its own site. When posting a new video, send only the M<permalinkUrl> property; videos on supported sites will be discovered and the embed code generated automatically.', 'string')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

}

class TPRelationship extends TPObject {

    protected static $properties = array(
        'source' => array('The source entity of the relationship.', 'Entity'),
        'status' => array('An object describing all the types of relationship that currently exist between the source and target objects.', 'RelationshipStatus'),
        'target' => array('The target entity of the relationship.', 'Entity'),
        'id' => array('A URI that serves as a globally unique identifier for the relationship.', 'string'),
        'urlId' => array('A string containing the canonical identifier that can be used to identify this object in URLs. This can be used to recognise where the same relationship is returned in response to different requests, and as a mapping key for an application\'s local data store.', 'string'),
        'created' => array('A mapping of the relationship types present between the source and target objects to the times those types of relationship were established. The keys of the map are the relationship type URIs present in the relationship\'s M<status> property; the values are W3CDTF timestamps for the times those relationship edges were created.', 'map<string>')
    );

    function __get($name) { return $this->get($name, self::$properties); }
    function __set($name, $value) { $this->set($name, $value, self::$properties); }
    static function propertyDocString($name) { return self::$properties[$name][0]; }
    static function propertyType($name) { return self::$properties[$name][1]; }
    function asPayload($properties = NULL, $want_json = 1) { return parent::asPayload($properties ? $properties : self::$properties, $want_json); }

    static function isAbstract() { return false; }

    function fulfill($data) {
        parent::fulfill($data);
        if (isset($data->source) && (get_class($data->source) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->source->objectType) ? $data->source->objectType : 'Entity');
            $this->source = new $ot_class($data->source);
        }
        if (isset($data->status) && (get_class($data->status) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->status->objectType) ? $data->status->objectType : 'RelationshipStatus');
            $this->status = new $ot_class($data->status);
        }
        if (isset($data->target) && (get_class($data->target) == 'stdClass')) {
            $ot_class = 'TP' . (isset($data->target->objectType) ? $data->target->objectType : 'Entity');
            $this->target = new $ot_class($data->target);
        }
    }
}

?>