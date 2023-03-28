export function permissionsCategoryColor(category) {
    const permissions_category_color = {
        land: '#32B87C',
        building: '#3970AA',
        machine: '#A9AA55',
        user: '#5655AA',
        role: '#F56C6C',
        reports: '#C08163',
        statistics: '#5F7B90',
        owner: '#B47BB4',
        revision: '#8F847B',
        system_setting: '#A36163',
        dashboard: '#32B87C',
    }

    return permissions_category_color[category]
}

export function zeroPad(num, places) {
    var zero = places - num.toString().length + 1;
    return Array(+(zero > 0 && zero)).join("0") + num;
}