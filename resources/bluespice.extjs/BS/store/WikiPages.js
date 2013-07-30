Ext.define('AM.store.Users', {
    extend: 'Ext.data.Store',
    fields: ['name', 'email'],
    data: [
        {name: 'Ed',    email: 'ed@sencha.com'},
        {name: 'Tommy', email: 'tommy@sencha.com'}
    ]
});