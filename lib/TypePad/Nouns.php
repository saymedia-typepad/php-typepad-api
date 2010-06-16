<?php

class TPFavorites extends TPNoun {

    function delete($params) {
        // Delete the selected favorite.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('favorites', $params['id']);
        return $this->typepad->delete($path_chunks, 'Favorite');
    }

    function get($params) {
        // Get basic information about the selected favorite, including its owner and the target asset.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('favorites', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'Favorite');
    }

}
TypePad::addNoun('favorites');

class TPBlogs extends TPNoun {

    function get($params) {
        // Get basic information about the selected blog.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'Blog');
    }

    function addCategory($params) {
        // Send label argument to remove a category from the blog
        $path_chunks = array('blogs', $params['id'], 'add-category');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

    function getCategories($params) {
        // Get a list of categories which are defined for the selected blog.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'categories');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<string>');
    }

    function getCommentingSettings($params) {
        // Get the commenting-related settings for this blog.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'commenting-settings');
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'BlogCommentingSettings');
    }

    function getPublishedComments($params) {
        // Return a pageable list of published comments associated with the selected blog
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'comments', '@published');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Comment>');
    }

    function getPublishedRecentComments($params) {
        // Return the fifty most recent published comments associated with the selected blog
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'comments', '@published', '@recent');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Comment>');
    }

    function getCrosspostAccounts($params) {
        // Get  a list of accounts that can be used for crossposting with this blog.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'crosspost-accounts');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Account>');
    }

    function discoverExternalPostAsset($params) {
        // If the selected blog is a connected blog, create or retrieve the external post stub for the given permalink.
        $path_chunks = array('blogs', $params['id'], 'discover-external-post-asset');
        return $this->typepad->post($path_chunks, $params['payload'], 'asset:Asset');
    }

    function postToMediaAssets($params) {
        // Add a new media asset to the account that owns this blog.
        $path_chunks = array('blogs', $params['id'], 'media-assets');
        return $this->typepad->post($path_chunks, $params['payload'], 'Asset');
    }

    function postToPageAssets($params) {
        // Add a new page to a blog
        $path_chunks = array('blogs', $params['id'], 'page-assets');
        return $this->typepad->post($path_chunks, $params['payload'], 'Page');
    }

    function getPageAssets($params) {
        // Get a list of pages associated with the selected blog.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'page-assets');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Page>');
    }

    function postToPostAssets($params) {
        // Add a new post to a blog
        $path_chunks = array('blogs', $params['id'], 'post-assets');
        return $this->typepad->post($path_chunks, $params['payload'], 'Post');
    }

    function getPostAssets($params) {
        // Get a list of posts associated with the selected blog.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'post-assets');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Post>');
    }

    function getPostAssetsByCategory($params) {
        // Get all visibile posts in the selected blog that have been assigned to the given category.
        $path_chunks = array('blogs', $params['id'], 'post-assets', '@by-category', $params['category']);
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Post>');
    }

    function getPostAssetsByMonth($params) {
        // Get all visible posts in the selected blog that have a publication date within the selected month, specified as a string of the form "YYYY-MM".
        $path_chunks = array('blogs', $params['id'], 'post-assets', '@by-month', $params['month']);
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Post>');
    }

    function getRecentPostAssets($params) {
        // Get the most recent 50 posts in the selected blog, including draft and scheduled posts.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'post-assets', '@recent');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Post>');
    }

    function getPostByEmailSettingsByUser($params) {
        // Get the selected user's post-by-email address
        $path_chunks = array('blogs', $params['id'], 'post-by-email-settings', '@by-user', $params['userId']);
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'PostByEmailAddress');
    }

    function removeCategory($params) {
        // Send label argument to remove a category from the blog
        $path_chunks = array('blogs', $params['id'], 'remove-category');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

    function getStats($params) {
        // Get data about the pageviews for the selected blog.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('blogs', $params['id'], 'stats');
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'BlogStats');
    }

}
TypePad::addNoun('blogs');

class TPRelationships extends TPNoun {

    function get($params) {
        // Get basic information about the selected relationship.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('relationships', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getStatus($params) {
        // Get the status information for the selected relationship, including its types.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('relationships', $params['id'], 'status');
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'RelationshipStatus');
    }

    function putStatus($params) {
        // Change the status information for the selected relationship, including its types.
        $path_chunks = array('relationships', $params['id'], 'status');
        return $this->typepad->put($path_chunks, $params['payload'], 'RelationshipStatus');
    }

}
TypePad::addNoun('relationships');

class TPObjectTypes extends TPNoun {

    function getAll($params) {
        // Get information about all of the object types in the API, including the names and types of their properties.
        $path_chunks = array('object-types');
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'List<ObjectType>');
    }

    function get($params) {
        // Get information about the selected object type and its properties.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('object-types', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'ObjectType');
    }

}
TypePad::addNoun('objectTypes');

class TPApplications extends TPNoun {

    function get($params) {
        // Get basic information about the selected application.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('applications', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'Application');
    }

    function createExternalFeedSubscription($params) {
        // Subscribe the application to one or more external feeds.
        $path_chunks = array('applications', $params['id'], 'create-external-feed-subscription');
        return $this->typepad->post($path_chunks, $params['payload'], 'subscription:ExternalFeedSubscription');
    }

    function getExternalFeedSubscriptions($params) {
        // Get a list of the application's active external feed subscriptions.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('applications', $params['id'], 'external-feed-subscriptions');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<ExternalFeedSubscription>');
    }

    function getGroups($params) {
        // Get a list of groups in which a client using a C<app_full> access auth token from this application can act.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('applications', $params['id'], 'groups');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Group>');
    }

}
TypePad::addNoun('applications');

class TPUsers extends TPNoun {

    function get($params) {
        // Get basic information about the selected user.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'User');
    }

    function getBlogs($params) {
        // Get a list of blogs that the selected user has access to.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'blogs');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Blog>');
    }

    function getElsewhereAccounts($params) {
        // Get a list of elsewhere accounts for the selected user.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'elsewhere-accounts');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Account>');
    }

    function getEvents($params) {
        // Get a list of events describing actions that the selected user performed.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'events');
        $query_params = array();
        if (array_key_exists('startToken', $params)) $query_params['start-token'] = $params['startToken'];
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        return $this->typepad->get($path_chunks, $query_params, 'Stream<Event>');
    }

    function getEventsByGroup($params) {
        // Get a list of events describing actions that the selected user performed in a particular group.
        $path_chunks = array('users', $params['id'], 'events', '@by-group', $params['groupId']);
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Event>');
    }

    function postToFavorites($params) {
        // Create a new favorite in the selected user's list of favorites.
        $path_chunks = array('users', $params['id'], 'favorites');
        return $this->typepad->post($path_chunks, $params['payload'], 'Favorite');
    }

    function getFavorites($params) {
        // Get a list of favorites that were listed by the selected user.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'favorites');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Favorite>');
    }

    function getMemberships($params) {
        // Get a list of relationships that the selected user has with groups.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'memberships');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getAdminMemberships($params) {
        // Get a list of relationships that have an Admin type that the selected user has with groups.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'memberships', '@admin');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getMembershipsByGroup($params) {
        // Get a list containing only the relationship between the selected user and a particular group, or an empty list if the user has no relationship with the group.
        $path_chunks = array('users', $params['id'], 'memberships', '@by-group', $params['groupId']);
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getMemberMemberships($params) {
        // Get a list of relationships that have a Member type that the selected user has with groups.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'memberships', '@member');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getNotifications($params) {
        // Get a list of events describing actions by users that the selected user is following.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'notifications');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Event>');
    }

    function getNotificationsByGroup($params) {
        // Get a list of events describing actions in a particular group by users that the selected user is following.
        $path_chunks = array('users', $params['id'], 'notifications', '@by-group', $params['groupId']);
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Event>');
    }

    function getProfile($params) {
        // Get a more extensive set of user properties that can be used to build a user profile page.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'profile');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'UserProfile');
    }

    function getRelationships($params) {
        // Get a list of relationships that the selected user has with other users, and that other users have with the selected user.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'relationships');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getRelationshipsByGroup($params) {
        // Get a list of relationships that the selected user has with other users, and that other users have with the selected user, constrained to members of a particular group.
        $path_chunks = array('users', $params['id'], 'relationships', '@by-group', $params['groupId']);
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getRelationshipsByUser($params) {
        // Get a list of relationships that the selected user has with a single other user.
        $path_chunks = array('users', $params['id'], 'relationships', '@by-user', $params['userId']);
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getFollowerRelationships($params) {
        // Get a list of relationships that have the Contact type that the selected user has with other users.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'relationships', '@follower');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getFollowingRelationships($params) {
        // Get a list of relationships that have the Contact type that other users have with the selected user.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('users', $params['id'], 'relationships', '@following');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

}
TypePad::addNoun('users');

class TPAssets extends TPNoun {

    function search($params) {
        // Search for user-created content across the whole of TypePad.
        $path_chunks = array('assets');
        $query_params = array();
        if (array_key_exists('filterByGroup', $params)) $query_params['filter.group'] = $params['filterByGroup'];
        if (array_key_exists('filterByAuthor', $params)) $query_params['filter.author'] = $params['filterByAuthor'];
        if (array_key_exists('startToken', $params)) $query_params['start-token'] = $params['startToken'];
        if (array_key_exists('filterByAssetRank', $params)) $query_params['filter.asset-rank'] = $params['filterByAssetRank'];
        if (array_key_exists('filterByOwner', $params)) $query_params['filter.owner'] = $params['filterByOwner'];
        if (array_key_exists('sort', $params)) $query_params['sort'] = $params['sort'];
        if (array_key_exists('q', $params)) $query_params['q'] = $params['q'];
        if (array_key_exists('filterByBlog', $params)) $query_params['filter.blog'] = $params['filterByBlog'];
        if (array_key_exists('filterByAssetType', $params)) $query_params['filter.asset-type'] = $params['filterByAssetType'];
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        return $this->typepad->get($path_chunks, $query_params, 'Stream<Asset>');
    }

    function delete($params) {
        // Delete the selected asset and its associated events, comments and favorites.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id']);
        return $this->typepad->delete($path_chunks, 'Asset');
    }

    function get($params) {
        // Get basic information about the selected asset.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'Asset');
    }

    function put($params) {
        // Update the selected asset.
        $path_chunks = array('assets', $params['id']);
        return $this->typepad->put($path_chunks, $params['payload'], 'Asset');
    }

    function addCategory($params) {
        // Send label argument to add a category to an asset
        $path_chunks = array('assets', $params['id'], 'add-category');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

    function getCategories($params) {
        // Get a list of categories into which this asset has been placed within its blog. Currently supported only for O<Post> assets that are posted within a blog.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id'], 'categories');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<string>');
    }

    function getCommentTree($params) {
        // Get a list of assets that were posted in response to the selected asset and their depth in the response tree
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id'], 'comment-tree');
        $query_params = array();
        if (array_key_exists('selectedItem', $params)) $query_params['selected-item'] = $params['selectedItem'];
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<CommentTreeItem>');
    }

    function postToComments($params) {
        // Create a new Comment asset as a response to the selected asset.
        $path_chunks = array('assets', $params['id'], 'comments');
        return $this->typepad->post($path_chunks, $params['payload'], 'Comment');
    }

    function getComments($params) {
        // Get a list of assets that were posted in response to the selected asset.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id'], 'comments');
        $query_params = array();
        if (array_key_exists('selectedItem', $params)) $query_params['selected-item'] = $params['selectedItem'];
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Comment>');
    }

    function getFavorites($params) {
        // Get a list of favorites that have been created for the selected asset.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id'], 'favorites');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Favorite>');
    }

    function getFeedbackStatus($params) {
        // Get the feedback status of selected asset
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id'], 'feedback-status');
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'FeedbackStatus');
    }

    function putFeedbackStatus($params) {
        // Set the feedback status of selected asset
        $path_chunks = array('assets', $params['id'], 'feedback-status');
        return $this->typepad->put($path_chunks, $params['payload'], 'FeedbackStatus');
    }

    function makeCommentPreview($params) {
        // Send relevant data to get back a model of what the submitted comment will look like.
        $path_chunks = array('assets', $params['id'], 'make-comment-preview');
        return $this->typepad->post($path_chunks, $params['payload'], 'comment:Asset');
    }

    function getMediaAssets($params) {
        // Get a list of media assets that are embedded in the content of the selected asset.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id'], 'media-assets');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Asset>');
    }

    function getPublicationStatus($params) {
        // Get the publication status of selected asset
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id'], 'publication-status');
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'PublicationStatus');
    }

    function putPublicationStatus($params) {
        // Set the publication status of selected asset
        $path_chunks = array('assets', $params['id'], 'publication-status');
        return $this->typepad->put($path_chunks, $params['payload'], 'PublicationStatus');
    }

    function getReblogs($params) {
        // Get a list of posts that were posted as reblogs of the selected asset.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('assets', $params['id'], 'reblogs');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Post>');
    }

    function removeCategory($params) {
        // Send label argument to remove a category from an asset
        $path_chunks = array('assets', $params['id'], 'remove-category');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

    function updatePublicationStatus($params) {
        // Adjust publication status of an asset
        $path_chunks = array('assets', $params['id'], 'update-publication-status');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

}
TypePad::addNoun('assets');

class TPNouns extends TPNoun {

    function getAll($params) {
        // Get information about all of the nouns in the API, along with their sub-resources and filters.
        $path_chunks = array('nouns');
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'List<Endpoint>');
    }

    function get($params) {
        // Get information about the selected noun, its sub-resources and their filters.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('nouns', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'Endpoint');
    }

}
TypePad::addNoun('nouns');

class TPApiKeys extends TPNoun {

    function get($params) {
        // Get basic information about the selected API key, including what application it belongs to.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('api-keys', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'ApiKey');
    }

}
TypePad::addNoun('apiKeys');

class TPEvents extends TPNoun {

    function get($params) {
        // Get basic information about the selected event.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('events', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'Event');
    }

}
TypePad::addNoun('events');

class TPGroups extends TPNoun {

    function get($params) {
        // Get basic information about the selected group.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('groups', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'Group');
    }

    function postToAudioAssets($params) {
        // Create a new Audio asset within the selected group.
        $path_chunks = array('groups', $params['id'], 'audio-assets');
        return $this->typepad->post($path_chunks, $params['payload'], 'Audio');
    }

    function createExternalFeedSubscription($params) {
        // Subscribe the group to one or more external feeds.
        $path_chunks = array('groups', $params['id'], 'create-external-feed-subscription');
        return $this->typepad->post($path_chunks, $params['payload'], 'subscription:ExternalFeedSubscription');
    }

    function getEvents($params) {
        // Get a list of events describing actions performed in the selected group.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('groups', $params['id'], 'events');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Event>');
    }

    function getExternalFeedSubscriptions($params) {
        // Get a list of the group's active external feed subscriptions.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('groups', $params['id'], 'external-feed-subscriptions');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<ExternalFeedSubscription>');
    }

    function postToLinkAssets($params) {
        // Create a new Link asset within the selected group.
        $path_chunks = array('groups', $params['id'], 'link-assets');
        return $this->typepad->post($path_chunks, $params['payload'], 'Link');
    }

    function getMemberships($params) {
        // Get a list of relationships between users and the selected group.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('groups', $params['id'], 'memberships');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getAdminMemberships($params) {
        // Get a list of relationships that have the Admin type between users and the selected group.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('groups', $params['id'], 'memberships', '@admin');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getBlockedMemberships($params) {
        // Get a list of relationships that have the Blocked type between users and the selected groups. (Restricted to group admin.)
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('groups', $params['id'], 'memberships', '@blocked');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function getMemberMemberships($params) {
        // Get a list of relationships that have the Member type between users and the selected group.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('groups', $params['id'], 'memberships', '@member');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<Relationship>');
    }

    function postToPhotoAssets($params) {
        // Create a new Photo asset within the selected group.
        $path_chunks = array('groups', $params['id'], 'photo-assets');
        return $this->typepad->post($path_chunks, $params['payload'], 'Photo');
    }

    function postToPostAssets($params) {
        // Create a new Post asset within the selected group.
        $path_chunks = array('groups', $params['id'], 'post-assets');
        return $this->typepad->post($path_chunks, $params['payload'], 'Post');
    }

    function postToVideoAssets($params) {
        // Create a new Video asset within the selected group.
        $path_chunks = array('groups', $params['id'], 'video-assets');
        return $this->typepad->post($path_chunks, $params['payload'], 'Video');
    }

}
TypePad::addNoun('groups');

class TPExternalFeedSubscriptions extends TPNoun {

    function delete($params) {
        // Remove the selected subscription.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('external-feed-subscriptions', $params['id']);
        return $this->typepad->delete($path_chunks, 'ExternalFeedSubscription');
    }

    function get($params) {
        // Get basic information about the selected subscription.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('external-feed-subscriptions', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'ExternalFeedSubscription');
    }

    function addFeeds($params) {
        // Add one or more feed identifiers to the subscription.
        $path_chunks = array('external-feed-subscriptions', $params['id'], 'add-feeds');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

    function getFeeds($params) {
        // Get a list of strings containing the identifiers of the feeds to which this subscription is subscribed.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('external-feed-subscriptions', $params['id'], 'feeds');
        $query_params = array();
        if (array_key_exists('limit', $params)) $query_params['max-results'] = $params['limit'];
        if (array_key_exists('offset', $params)) $query_params['start-index'] = $params['offset'] + 1;
        return $this->typepad->get($path_chunks, $query_params, 'List<string>');
    }

    function removeFeeds($params) {
        // Remove one or more feed identifiers from the subscription.
        $path_chunks = array('external-feed-subscriptions', $params['id'], 'remove-feeds');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

    function updateFilters($params) {
        // Change the filtering rules for the subscription.
        $path_chunks = array('external-feed-subscriptions', $params['id'], 'update-filters');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

    function updateNotificationSettings($params) {
        // Change the callback URL for the subscription.
        $path_chunks = array('external-feed-subscriptions', $params['id'], 'update-notification-settings');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

    function updateUser($params) {
        // Change the "post as" user for a subscription owned by a group.
        $path_chunks = array('external-feed-subscriptions', $params['id'], 'update-user');
        return $this->typepad->post($path_chunks, $params['payload'], '');
    }

}
TypePad::addNoun('externalFeedSubscriptions');

class TPAuthTokens extends TPNoun {

    function get($params) {
        // Get basic information about the selected auth token, including what object it grants access to.
       if (!is_array($params)) $params = array('id' => $params);
        $path_chunks = array('auth-tokens', $params['id']);
        $query_params = array();
        return $this->typepad->get($path_chunks, $query_params, 'AuthToken');
    }

}
TypePad::addNoun('authTokens');

?>