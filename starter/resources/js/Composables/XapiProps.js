import moment from "moment";

export function useXapiProps() {
    return {
        actorType,
        actorName,
        verb,
        objectType,
        objectName,
        storedDate,
        storedTime,
        activityType,
        activityName,
        activityJson,
        activityIsProfile,
        agentId,
        agentIdBySid,
        agentIdByJson,
        agentJson,
        agentJsonIdBySid,
        agentName,
        agentCountMembers,
        documentData,
    };
}

// ----------------------------------------- STATEMENT ------------------------------------------ //

const actorType = (actor) => {
    return actor.objectType == "Group" ? "Group" : "Agent";
};

const actorName = (actor) => {
    let name = "anonymous";
    if (actor.name) {
        name = actor.name;
    } else {
        if (actor.mbox) {
            name = actor.mbox.substring(7);
        }
        if (actor.mbox_sha1sum) {
            name = actor.mbox_sha1sum;
        }
        if (actor.openid) {
            name = actor.openid;
        }
        if (actor.account) {
            name = actor.account.name;
        }
    }
    return name;
};

const verb = (verb) => {
    return String(verb.id).split("/").pop();
};

const objectType = (object) => {
    return object.objectType ? object.objectType : "Activity";
};

const objectName = (object) => {
    if (object.definition && object.definition.name) {
        return object.definition.name[Object.keys(object.definition.name)[0]];
    }
    if (object.id) {
        return String(object.id).split("/").pop();
    }
    if (object.objectType == "Agent" || object.objectType == "Group") {
        return actorName(object);
    }
    return "";
};

const storedDate = (stored) => {
    return moment(stored).format("YYYY-MM-DD");
};

const storedTime = (stored) => {
    return moment(stored).format("HH:mm:ss");
};

// ----------------------------------------- ACTIVITY ------------------------------------------ //

const activityType = (activity) => {
    if (activity.definition && activity.definition.type) {
        return String(activity.definition.type).split("/").pop();
    }
    return "";
};

const activityName = (activity) => {
    if (activity.definition && activity.definition.name) {
        return activity.definition.name[
            Object.keys(activity.definition.name)[0]
        ];
    }
    return "";
};

const activityJson = (record) => {
    return {
        id: record.iri,
        definition: record.definition,
    };
};

const activityIsProfile = (activity) => {
    return (
        activity.definition &&
        activity.definition.type ==
            "http://adlnet.gov/expapi/activities/profile"
    );
};

// ----------------------------------------- AGENT ------------------------------------------ //

const agentId = (record) => {
    let id = record.sid_field_1.replace("mbox::", "");
    return record.sid_field_2 ? id + " | " + record.sid_field_2 : id;
};

const agentJson = (record) => {
    let json = {
        objectType: record.is_group ? "Group" : "Agent",
    };
    if (record.name) {
        json.name = record.name;
    }
    if (record.sid_type == "mbox") {
        json.mbox = record.sid_field_1;
    }
    if (record.sid_type == "mbox_sha1sum") {
        json.mbox_sha1sum = record.sid_field_1;
    }
    if (record.sid_type == "openid") {
        json.openid = record.sid_field_1;
    }
    if (record.sid_type == "account") {
        json.account = {
            name: record.sid_field_1,
            homePage: record.sid_field_2,
        };
    }
    if (record.members.length) {
        json.member = record.members;
    }
    return json;
};

const agentName = (record) => {
    return record.name ? record.name : "";
};

const agentCountMembers = (record) => {
    return record.members.length;
};

const agentJsonIdBySid = (sid) => {
    let json = {};
    let type = sid.split("::")[0];
    if (type == "mbox") {
        json.mbox = sid.replace("mbox::", "");
    }
    if (type == "mbox_sha1sum") {
        json.mbox_sha1sum = sid.replace("mbox_sha1sum::", "");
    }
    if (type == "openid") {
        json.openid = sid.replace("openid::", "");
    }
    if (type == "account") {
        let parts = sid.replace("account::", "").split("@");
        json.account = {
            name: parts[0],
            homePage: parts[1],
        };
    }
    return json;
};

const agentIdBySid = (sid) => {
    let type = sid.split("::")[0];
    let id = type == "account" ? sid.replace("@", " | ") : sid;
    return id
        .replace("mbox::mailto:", "")
        .replace("mbox_sha1sum::", "")
        .replace("openid::", "")
        .replace("account::", "");
};

const agentSid = (agent) => {
    if (agent.mbox) {
        return "mbox::" + agent.mbox;
    }
    if (agent.mbox_sha1sum) {
        return "mbox_sha1sum::" + agent.mbox_sha1sum;
    }
    if (agent.openid) {
        return "openid::" + agent.openid;
    }
    if (agent.account) {
        return "account::" + agent.account.name + "@" + agent.account.homePage;
    }
};

const agentIdByJson = (agent) => {
    return agentIdBySid(agentSid(agent));
};

// ----------------------------------------- DOCUMENT ------------------------------------------ //

const documentData = (record) => {
    return record.content_type == "application/json"
        ? JSON.parse(record.content)
        : record.content;
};
