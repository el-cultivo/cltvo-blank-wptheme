import {$, w} from './constants';

w.load(() => {
    console.log('Hello world from El Cultivo! --Load');
});

$(document).ready(function() {
    console.log('Hello world from El Cultivo! --Ready');

})
