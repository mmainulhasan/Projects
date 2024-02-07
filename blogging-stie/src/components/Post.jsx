import React, { useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";

const Post = () => {
    const { id } = useParams();
    const [post, setPost] = useState(null);
    const [likeCount, setLikeCount] = useState(0);
    const [dislikeCount, setDislikeCount] = useState(0);
    const [ipAddress, setIpAddress] = useState("");

    const fetchPost = async () => {
        try {
            const response = await axios.get(`${process.env.REACT_APP_API_BASE_URL}/post.php/${id}`);
            const post = response.data.data;
            setPost(post);
            setLikeCount(post.likes);
            setDislikeCount(post.dislikes);
        } catch (error) {
            console.log(error);
        }
    };

    const fetchIpAddress = async () => {
        try {
            const response = await axios.get("https://api.ipify.org/?format=json");
            setIpAddress(response.data.ip);
        } catch (error) {
            console.log(error);
        }
    };

    const handleLike = async () => {
        try {
            const response = await axios.post(`${process.env.REACT_APP_API_BASE_URL}/post.php/${id}/like/${ipAddress}`);
            const likes = response.data.data;
            setLikeCount(likes);
        } catch (error) {
            console.log(error);
        }
    };

    const handleDislike = async () => {
        try {
            const response = await axios.post(`${process.env.REACT_APP_API_BASE_URL}/post.php/${id}/dislike/${ipAddress}`);
            const dislikes = response.data.data;
            setDislikeCount(dislikes);
        } catch (error) {
            console.log(error);
        }
    };

    React.useEffect(() => {
        fetchPost();
        fetchIpAddress();
    }, []);

    if (!post) {
        return <div>Loading...</div>;
    }

    return (
        <div className="container my-4">
            <h1 className="mb-4">{post.title}</h1>
            <p>{post.content}</p>
            <hr />
            <div className="d-flex justify-content-between">
                <div>
                    <button className="btn btn-outline-primary me-2" onClick={handleLike}>
                        Like <span className="badge bg-primary">{likeCount}</span>
                    </button>
                    <button className="btn btn-outline-danger" onClick={handleDislike}>
                        Dislike <span className="badge bg-danger">{dislikeCount}</span>
                    </button>
                </div>
                <div>
                    <small className="text-muted">
                        Posted by {post.author} on {post.date}
                    </small>
                </div>
            </div>
        </div>
    );
};

export default Post;