import TutorialSearch from './components/TutorialSearch'

$(function() {
    window.Concrete.Vue.createContext('frontend', {
        TutorialSearch
    }, 'frontend')
})

require("./latest-video");
require("./disclosure");
require("./translate");
require("./switch-release-version");

import hljs from 'highlight.js';

hljs.highlightAll();
