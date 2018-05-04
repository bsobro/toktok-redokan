contentEgg.factory('ModuleService', ['$http', '$timeout', function ($http, $timeout) {

        var service = function (module_id) {
            this.module_id = module_id;
            this.results = [];
            this.added = [];
            this.added_changed = false;
            this.processing = false;
            this.loaded = false;
            this.error = '';
        };

        service.prototype.find = function (query) {
            var self = this;

            self.processing = true;

            var params = {
                'action': 'content-egg-module-api',
                'module': this.module_id,
                'query': query,
                '_contentegg_nonce': contentegg_params.nonce,
            };

            return $http({
                method: 'GET',
                url: ajaxurl,
                params: params
            }).then(function (response) {
                var data = response.data;
                if (!data.error)
                {
                    self.results = data.results;
                    self.error = '';
                    self.loaded = true;
                } else {
                    self.error = data.error;
                }
                $timeout(function () {
                    self.processing = false;
                }, 1000);

                return self.results;
            }, function (error) {
                self.processing = false;
                self.error = error;
                //console.error(module + ' error: ' + error);
            });


        };
        return service;
    }]);