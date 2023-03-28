import Vue from "vue";

Vue.prototype.$can = function(param='') {
    if (Object.entries(this.$auth?.user || [])?.length) {
        // var permit = store?.getters?.authUser?.roles?.reduce((permits, role)=>{
        //     role?.permissions?.forEach(p => {
        //         if (!permits?.includes(p?.name)) {
        //             if (!permits?.includes(p?.name?.split('-')[1])) {
        //                 permits?.push(p?.name?.split('-')[1])
        //             }
        //             permits?.push(p?.name)
        //         }
        //     });
        //     return permits
        // }, [])
        var permit = this.$auth?.user.permissionsArray || []
        return typeof param == 'string' ? permit?.includes(param) : param?.some(p=>permit?.includes(p))
    }
    return false
}

String.prototype.ucfirst = function () {
    return this.charAt(0).toUpperCase() + this.slice(1).toLowerCase();
};

/**
 * fNum format number with comma and decimal places
 * @param  {Number} decimal_places optional Number default "2"
 * @return {String} comma separated
 */
Number.prototype.fNum = function (decimal_places=2) {
    return this.toLocaleString('en-GB', {
        minimumFractionDigits: decimal_places,
        maximumFractionDigits: decimal_places
    })
};
